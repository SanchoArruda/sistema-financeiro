<?php
/**
 * Finzy — Controller de Relatórios e Exportações (RelatorioController)
 * 
 * Responsável pelo módulo de relatórios financeiros (Movimentações, Despesas Pendentes,
 * Receitas Pendentes, Resumo por Categoria) e geração de exportações em CSV e PDF.
 */

if (!defined('FINZY_BOOTSTRAP')) {
    http_response_code(403);
    exit('Acesso proibido.');
}

require_once __DIR__ . '/../models/LancamentoModel.php';
require_once __DIR__ . '/../models/CategoriaModel.php';
require_once __DIR__ . '/../models/ContaModel.php';
require_once __DIR__ . '/../models/UsuarioModel.php';
require_once __DIR__ . '/../helpers/PdfReportHelper.php';

class RelatorioController {

    private LancamentoModel $lancamentoModel;
    private CategoriaModel $categoriaModel;
    private ContaModel $contaModel;
    private UsuarioModel $usuarioModel;

    public function __construct() {
        $this->lancamentoModel = new LancamentoModel();
        $this->categoriaModel = new CategoriaModel();
        $this->contaModel = new ContaModel();
        $this->usuarioModel = new UsuarioModel();
    }

    /**
     * Extrai e sanitiza os parâmetros de filtro passados via $_GET.
     * 
     * @return array
     */
    private function obterFiltrosSanitizados(): array {
        $dataInicio = $_GET['data_inicio'] ?? date('Y-m-01');
        $dataFim = $_GET['data_fim'] ?? date('Y-m-t');

        // Valida formato de data (AAAA-MM-DD)
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dataInicio)) {
            $dataInicio = date('Y-m-01');
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dataFim)) {
            $dataFim = date('Y-m-t');
        }

        $tipoRelatorio = $_GET['tipo_relatorio'] ?? 'movimentacoes';
        $tiposValidos = ['movimentacoes', 'despesas_pendentes', 'receitas_pendentes', 'resumo_categoria'];
        if (!in_array($tipoRelatorio, $tiposValidos, true)) {
            $tipoRelatorio = 'movimentacoes';
        }

        return [
            'tipo_relatorio'      => $tipoRelatorio,
            'data_inicio'         => $dataInicio,
            'data_fim'            => $dataFim,
            'tipo'                => $_GET['tipo'] ?? '',
            'categoria_id'        => (int)($_GET['categoria_id'] ?? 0),
            'conta_id'            => (int)($_GET['conta_id'] ?? 0),
            'situacao'            => $_GET['situacao'] ?? '',
            'criado_por'          => (int)($_GET['criado_por'] ?? 0),
            'busca'               => trim($_GET['busca'] ?? '')
        ];
    }

    /**
     * Tela principal de consulta aos relatórios financeiros.
     */
    public function index(): void {
        AuthHelper::requireLogin();

        $filtros = $this->obterFiltrosSanitizados();

        // Carrega opções para os selects dos filtros
        $categorias = $this->categoriaModel->listar(['status' => 'ativo']);
        $contas = $this->contaModel->listar(['status' => 'ativo']);
        $usuarios = $this->usuarioModel->listar();

        // Processa os dados e totalizadores do relatório ativo
        $dadosRelatorio = [];
        $totalizadores = [];

        switch ($filtros['tipo_relatorio']) {
            case 'movimentacoes':
                $dadosRelatorio = $this->lancamentoModel->obterRelatorioMovimentacoes($filtros);
                $saldoInicial = $this->lancamentoModel->obterSaldoInicialPeriodo($filtros['data_inicio'], $filtros['conta_id'] ?: null);
                
                $totalReceitas = 0.0;
                $totalDespesas = 0.0;
                foreach ($dadosRelatorio as $row) {
                    if ($row['tipo'] === 'receita') {
                        $totalReceitas += (float)$row['valor'];
                    } else {
                        $totalDespesas += (float)$row['valor'];
                    }
                }
                $saldoFinal = $saldoInicial + $totalReceitas - $totalDespesas;

                $totalizadores = [
                    'saldo_inicial'  => $saldoInicial,
                    'total_receitas' => $totalReceitas,
                    'total_despesas' => $totalDespesas,
                    'saldo_final'    => $saldoFinal,
                    'total_itens'    => count($dadosRelatorio)
                ];
                break;

            case 'despesas_pendentes':
                $dadosRelatorio = $this->lancamentoModel->obterRelatorioDespesasPendentes($filtros);
                $totalPendentes = 0.0;
                foreach ($dadosRelatorio as $row) {
                    $totalPendentes += (float)$row['valor'];
                }
                $totalizadores = [
                    'total_pendente' => $totalPendentes,
                    'total_itens'    => count($dadosRelatorio)
                ];
                break;

            case 'receitas_pendentes':
                $dadosRelatorio = $this->lancamentoModel->obterRelatorioReceitasPendentes($filtros);
                $totalPendentes = 0.0;
                foreach ($dadosRelatorio as $row) {
                    $totalPendentes += (float)$row['valor'];
                }
                $totalizadores = [
                    'total_pendente' => $totalPendentes,
                    'total_itens'    => count($dadosRelatorio)
                ];
                break;

            case 'resumo_categoria':
                $dadosRelatorio = $this->lancamentoModel->obterRelatorioResumoCategoria($filtros);
                $totalReceitas = 0.0;
                $totalDespesas = 0.0;
                foreach ($dadosRelatorio as $row) {
                    if (($row['lancamento_tipo'] ?? $row['categoria_tipo']) === 'receita') {
                        $totalReceitas += (float)$row['total_valor'];
                    } else {
                        $totalDespesas += (float)$row['total_valor'];
                    }
                }
                $totalizadores = [
                    'total_receitas' => $totalReceitas,
                    'total_despesas' => $totalDespesas,
                    'total_geral'    => $totalReceitas + $totalDespesas,
                    'total_itens'    => count($dadosRelatorio)
                ];
                break;
        }

        $tituloPagina = 'Relatórios Financeiros — ' . APP_NAME;
        require __DIR__ . '/../views/layouts/header.php';
        require __DIR__ . '/../views/relatorios/index.php';
        require __DIR__ . '/../views/layouts/footer.php';
    }

    /**
     * Exportação do relatório em formato CSV nativo em UTF-8 com BOM e separador ';'.
     */
    public function exportarCsv(): void {
        AuthHelper::requireLogin();

        $filtros = $this->obterFiltrosSanitizados();
        $filename = 'relatorio_' . $filtros['tipo_relatorio'] . '_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');

        $out = fopen('php://output', 'w');
        // Insere o BOM UTF-8 para compatibilidade nativa com o Excel
        fwrite($out, "\xEF\xBB\xBF");

        switch ($filtros['tipo_relatorio']) {
            case 'movimentacoes':
                $dados = $this->lancamentoModel->obterRelatorioMovimentacoes($filtros);
                fputcsv($out, ['Data', 'Tipo', 'Descrição', 'Categoria', 'Conta', 'Forma de Pagamento', 'Valor (R$)', 'Situação', 'Criado Por'], ';');

                foreach ($dados as $row) {
                    $situacaoLabel = $row['situacao'] === 'realizado' ? 'Realizado' : ($row['situacao'] === 'atrasado' ? 'Em atraso' : 'Pendente');
                    fputcsv($out, [
                        FormatHelper::data($row['data_lancamento']),
                        ucfirst($row['tipo']),
                        $row['descricao'],
                        $row['categoria_nome'],
                        $row['conta_nome'],
                        $row['forma_pagamento_nome'],
                        number_format((float)$row['valor'], 2, ',', ''),
                        $situacaoLabel,
                        $row['criador_nome']
                    ], ';');
                }
                break;

            case 'despesas_pendentes':
                $dados = $this->lancamentoModel->obterRelatorioDespesasPendentes($filtros);
                fputcsv($out, ['Data do Lançamento', 'Descrição', 'Categoria', 'Conta', 'Valor (R$)', 'Situação', 'Criado Por'], ';');

                foreach ($dados as $row) {
                    $situacaoLabel = $row['situacao'] === 'atrasado' ? 'Em atraso' : 'Pendente';
                    fputcsv($out, [
                        FormatHelper::data($row['data_lancamento']),
                        $row['descricao'],
                        $row['categoria_nome'],
                        $row['conta_nome'],
                        number_format((float)$row['valor'], 2, ',', ''),
                        $situacaoLabel,
                        $row['criador_nome']
                    ], ';');
                }
                break;

            case 'receitas_pendentes':
                $dados = $this->lancamentoModel->obterRelatorioReceitasPendentes($filtros);
                fputcsv($out, ['Data do Lançamento', 'Descrição', 'Categoria', 'Conta', 'Valor (R$)', 'Situação', 'Criado Por'], ';');

                foreach ($dados as $row) {
                    fputcsv($out, [
                        FormatHelper::data($row['data_lancamento']),
                        $row['descricao'],
                        $row['categoria_nome'],
                        $row['conta_nome'],
                        number_format((float)$row['valor'], 2, ',', ''),
                        'Pendente',
                        $row['criador_nome']
                    ], ';');
                }
                break;

            case 'resumo_categoria':
                $dados = $this->lancamentoModel->obterRelatorioResumoCategoria($filtros);
                fputcsv($out, ['Categoria', 'Tipo da Categoria', 'Tipo do Lançamento', 'Total de Lançamentos', 'Total do Período (R$)'], ';');

                foreach ($dados as $row) {
                    fputcsv($out, [
                        $row['categoria_nome'],
                        ucfirst($row['categoria_tipo']),
                        ucfirst($row['lancamento_tipo'] ?? $row['categoria_tipo']),
                        (int)$row['total_lancamentos'],
                        number_format((float)$row['total_valor'], 2, ',', '')
                    ], ';');
                }
                break;
        }

        fclose($out);
        exit;
    }

    /**
     * Exportação do relatório em formato PDF A4 formatado.
     */
    public function exportarPdf(): void {
        AuthHelper::requireLogin();

        $filtros = $this->obterFiltrosSanitizados();
        $periodoTexto = FormatHelper::data($filtros['data_inicio']) . ' até ' . FormatHelper::data($filtros['data_fim']);

        $filtrosAtivos = [];
        if (!empty($filtros['tipo'])) {
            $filtrosAtivos[] = 'Tipo: ' . ucfirst($filtros['tipo']);
        }
        if ($filtros['categoria_id'] > 0) {
            $cat = $this->categoriaModel->buscarPorId($filtros['categoria_id']);
            if ($cat) $filtrosAtivos[] = 'Categoria: ' . $cat['nome'];
        }
        if ($filtros['conta_id'] > 0) {
            $cnt = $this->contaModel->buscarPorId($filtros['conta_id']);
            if ($cnt) $filtrosAtivos[] = 'Conta: ' . $cnt['nome'];
        }
        if (!empty($filtros['situacao'])) {
            $filtrosAtivos[] = 'Situação: ' . ucfirst($filtros['situacao']);
        }

        switch ($filtros['tipo_relatorio']) {
            case 'movimentacoes':
                $titulo = 'Relatório de Movimentações (Receitas e Despesas)';
                $dados = $this->lancamentoModel->obterRelatorioMovimentacoes($filtros);
                $saldoInicial = $this->lancamentoModel->obterSaldoInicialPeriodo($filtros['data_inicio'], $filtros['conta_id'] ?: null);
                
                $totalRec = 0.0; $totalDesp = 0.0;
                foreach ($dados as $r) {
                    if ($r['tipo'] === 'receita') $totalRec += (float)$r['valor'];
                    else $totalDesp += (float)$r['valor'];
                }
                $saldoFinal = $saldoInicial + $totalRec - $totalDesp;

                $pdf = new PdfReportHelper($titulo, $periodoTexto, $filtrosAtivos);
                $pdf->AddPage();

                $pdf->RenderSummaryBox([
                    'Saldo Inicial' => FormatHelper::moeda($saldoInicial),
                    'Total Receitas' => FormatHelper::moeda($totalRec),
                    'Total Despesas' => FormatHelper::moeda($totalDesp),
                    'Saldo Final' => FormatHelper::moeda($saldoFinal)
                ]);

                // Cabeçalho da Tabela
                $pdf->SetFont('Helvetica', 'B', 8);
                $pdf->SetFillColor(30, 58, 95); // Primary Container #1E3A5F
                $pdf->SetTextColor(255, 255, 255);

                $pdf->Cell(18, 7, $pdf->encodeText('Data'), 1, 0, 'C', true);
                $pdf->Cell(16, 7, $pdf->encodeText('Tipo'), 1, 0, 'C', true);
                $pdf->Cell(52, 7, $pdf->encodeText('Descrição'), 1, 0, 'L', true);
                $pdf->Cell(30, 7, $pdf->encodeText('Categoria'), 1, 0, 'L', true);
                $pdf->Cell(26, 7, $pdf->encodeText('Conta'), 1, 0, 'L', true);
                $pdf->Cell(24, 7, $pdf->encodeText('Valor (R$)'), 1, 0, 'R', true);
                $pdf->Cell(20, 7, $pdf->encodeText('Situação'), 1, 1, 'C', true);

                $pdf->SetFont('Helvetica', '', 7.5);
                $pdf->SetTextColor(26, 28, 30);
                $fill = false;

                foreach ($dados as $row) {
                    $pdf->SetFillColor($fill ? 244 : 255, $fill ? 243 : 255, $fill ? 247 : 255);
                    $situacaoLabel = $row['situacao'] === 'realizado' ? 'Realizado' : ($row['situacao'] === 'atrasado' ? 'Em atraso' : 'Pendente');

                    $pdf->Cell(18, 6, FormatHelper::data($row['data_lancamento']), 1, 0, 'C', true);
                    $pdf->Cell(16, 6, $pdf->encodeText(ucfirst($row['tipo'])), 1, 0, 'C', true);
                    $pdf->Cell(52, 6, $pdf->encodeText(mb_strimwidth($row['descricao'], 0, 32, '...')), 1, 0, 'L', true);
                    $pdf->Cell(30, 6, $pdf->encodeText(mb_strimwidth($row['categoria_nome'], 0, 18, '...')), 1, 0, 'L', true);
                    $pdf->Cell(26, 6, $pdf->encodeText(mb_strimwidth($row['conta_nome'], 0, 16, '...')), 1, 0, 'L', true);
                    $pdf->Cell(24, 6, FormatHelper::moeda((float)$row['valor']), 1, 0, 'R', true);
                    $pdf->Cell(20, 6, $pdf->encodeText($situacaoLabel), 1, 1, 'C', true);

                    $fill = !$fill;
                }
                break;

            case 'despesas_pendentes':
                $titulo = 'Relatório de Despesas Pendentes';
                $dados = $this->lancamentoModel->obterRelatorioDespesasPendentes($filtros);
                $totalPend = 0.0;
                foreach ($dados as $r) $totalPend += (float)$r['valor'];

                $pdf = new PdfReportHelper($titulo, $periodoTexto, $filtrosAtivos);
                $pdf->AddPage();

                $pdf->RenderSummaryBox([
                    'Total Despesas Pendentes' => FormatHelper::moeda($totalPend),
                    'Quantidade de Lançamentos' => count($dados)
                ]);

                $pdf->SetFont('Helvetica', 'B', 8);
                $pdf->SetFillColor(30, 58, 95);
                $pdf->SetTextColor(255, 255, 255);

                $pdf->Cell(22, 7, $pdf->encodeText('Data Lanç.'), 1, 0, 'C', true);
                $pdf->Cell(64, 7, $pdf->encodeText('Descrição'), 1, 0, 'L', true);
                $pdf->Cell(36, 7, $pdf->encodeText('Categoria'), 1, 0, 'L', true);
                $pdf->Cell(34, 7, $pdf->encodeText('Conta'), 1, 0, 'L', true);
                $pdf->Cell(30, 7, $pdf->encodeText('Valor (R$)'), 1, 1, 'R', true);

                $pdf->SetFont('Helvetica', '', 8);
                $pdf->SetTextColor(26, 28, 30);
                $fill = false;

                foreach ($dados as $row) {
                    $pdf->SetFillColor($fill ? 244 : 255, $fill ? 243 : 255, $fill ? 247 : 255);
                    $pdf->Cell(22, 6, FormatHelper::data($row['data_lancamento']), 1, 0, 'C', true);
                    $pdf->Cell(64, 6, $pdf->encodeText(mb_strimwidth($row['descricao'], 0, 42, '...')), 1, 0, 'L', true);
                    $pdf->Cell(36, 6, $pdf->encodeText(mb_strimwidth($row['categoria_nome'], 0, 22, '...')), 1, 0, 'L', true);
                    $pdf->Cell(34, 6, $pdf->encodeText(mb_strimwidth($row['conta_nome'], 0, 20, '...')), 1, 0, 'L', true);
                    $pdf->Cell(30, 6, FormatHelper::moeda((float)$row['valor']), 1, 1, 'R', true);
                    $fill = !$fill;
                }
                break;

            case 'receitas_pendentes':
                $titulo = 'Relatório de Receitas Pendentes';
                $dados = $this->lancamentoModel->obterRelatorioReceitasPendentes($filtros);
                $totalPend = 0.0;
                foreach ($dados as $r) $totalPend += (float)$r['valor'];

                $pdf = new PdfReportHelper($titulo, $periodoTexto, $filtrosAtivos);
                $pdf->AddPage();

                $pdf->RenderSummaryBox([
                    'Total Receitas Pendentes' => FormatHelper::moeda($totalPend),
                    'Quantidade de Lançamentos' => count($dados)
                ]);

                $pdf->SetFont('Helvetica', 'B', 8);
                $pdf->SetFillColor(30, 58, 95);
                $pdf->SetTextColor(255, 255, 255);

                $pdf->Cell(22, 7, $pdf->encodeText('Data Lanç.'), 1, 0, 'C', true);
                $pdf->Cell(64, 7, $pdf->encodeText('Descrição'), 1, 0, 'L', true);
                $pdf->Cell(36, 7, $pdf->encodeText('Categoria'), 1, 0, 'L', true);
                $pdf->Cell(34, 7, $pdf->encodeText('Conta'), 1, 0, 'L', true);
                $pdf->Cell(30, 7, $pdf->encodeText('Valor (R$)'), 1, 1, 'R', true);

                $pdf->SetFont('Helvetica', '', 8);
                $pdf->SetTextColor(26, 28, 30);
                $fill = false;

                foreach ($dados as $row) {
                    $pdf->SetFillColor($fill ? 244 : 255, $fill ? 243 : 255, $fill ? 247 : 255);
                    $pdf->Cell(22, 6, FormatHelper::data($row['data_lancamento']), 1, 0, 'C', true);
                    $pdf->Cell(64, 6, $pdf->encodeText(mb_strimwidth($row['descricao'], 0, 42, '...')), 1, 0, 'L', true);
                    $pdf->Cell(36, 6, $pdf->encodeText(mb_strimwidth($row['categoria_nome'], 0, 22, '...')), 1, 0, 'L', true);
                    $pdf->Cell(34, 6, $pdf->encodeText(mb_strimwidth($row['conta_nome'], 0, 20, '...')), 1, 0, 'L', true);
                    $pdf->Cell(30, 6, FormatHelper::moeda((float)$row['valor']), 1, 1, 'R', true);
                    $fill = !$fill;
                }
                break;

            case 'resumo_categoria':
                $titulo = 'Relatório de Resumo por Categoria';
                $dados = $this->lancamentoModel->obterRelatorioResumoCategoria($filtros);
                $totRec = 0.0; $totDesp = 0.0;
                foreach ($dados as $r) {
                    if (($r['lancamento_tipo'] ?? $r['categoria_tipo']) === 'receita') $totRec += (float)$r['total_valor'];
                    else $totDesp += (float)$r['total_valor'];
                }

                $pdf = new PdfReportHelper($titulo, $periodoTexto, $filtrosAtivos);
                $pdf->AddPage();

                $pdf->RenderSummaryBox([
                    'Total Receitas' => FormatHelper::moeda($totRec),
                    'Total Despesas' => FormatHelper::moeda($totDesp),
                    'Total Geral' => FormatHelper::moeda($totRec + $totDesp)
                ]);

                $pdf->SetFont('Helvetica', 'B', 8);
                $pdf->SetFillColor(30, 58, 95);
                $pdf->SetTextColor(255, 255, 255);

                $pdf->Cell(76, 7, $pdf->encodeText('Categoria'), 1, 0, 'L', true);
                $pdf->Cell(34, 7, $pdf->encodeText('Tipo'), 1, 0, 'C', true);
                $pdf->Cell(36, 7, $pdf->encodeText('Lançamentos'), 1, 0, 'C', true);
                $pdf->Cell(40, 7, $pdf->encodeText('Total do Período (R$)'), 1, 1, 'R', true);

                $pdf->SetFont('Helvetica', '', 8);
                $pdf->SetTextColor(26, 28, 30);
                $fill = false;

                foreach ($dados as $row) {
                    $pdf->SetFillColor($fill ? 244 : 255, $fill ? 243 : 255, $fill ? 247 : 255);
                    $pdf->Cell(76, 6, $pdf->encodeText($row['categoria_nome']), 1, 0, 'L', true);
                    $pdf->Cell(34, 6, $pdf->encodeText(ucfirst($row['lancamento_tipo'] ?? $row['categoria_tipo'])), 1, 0, 'C', true);
                    $pdf->Cell(36, 6, (string)$row['total_lancamentos'], 1, 0, 'C', true);
                    $pdf->Cell(40, 6, FormatHelper::moeda((float)$row['total_valor']), 1, 1, 'R', true);
                    $fill = !$fill;
                }
                break;
        }

        $pdf->Output('I', 'relatorio_' . $filtros['tipo_relatorio'] . '_' . date('Ymd_His') . '.pdf');
        exit;
    }
}
