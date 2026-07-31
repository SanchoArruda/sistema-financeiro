<?php
/**
 * Finzy — Helper de Geração de Relatórios em PDF (PdfReportHelper)
 * 
 * Subclasse de FPDF estilizada conforme o Design System Fiscal Precision.
 */

if (!defined('FINZY_BOOTSTRAP')) {
    http_response_code(403);
    exit('Acesso proibido.');
}

require_once __DIR__ . '/../../vendor/fpdf/fpdf.php';

class PdfReportHelper extends FPDF {

    private string $tituloRelatorio = '';
    private string $periodoTexto = '';
    private array $filtrosAtivos = [];

    public function __construct(string $tituloRelatorio, string $periodoTexto, array $filtrosAtivos = []) {
        parent::__construct('P', 'mm', 'A4');
        $this->tituloRelatorio = $tituloRelatorio;
        $this->periodoTexto = $periodoTexto;
        $this->filtrosAtivos = $filtrosAtivos;
        $this->AliasNbPages('{nb}');
        $this->SetAutoPageBreak(true, 18);
        $this->SetMargins(12, 12, 12);
    }

    /**
     * Converte texto UTF-8 para a codificação ISO-8859-1 utilizada pelo FPDF.
     * 
     * @param string|null $str
     * @return string
     */
    public function encodeText(?string $str): string {
        if ($str === null || $str === '') {
            return '';
        }
        if (function_exists('mb_convert_encoding')) {
            return mb_convert_encoding($str, 'ISO-8859-1', 'UTF-8');
        }
        return iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $str) ?: $str;
    }

    /**
     * Renderiza o cabeçalho padrão em todas as páginas do PDF.
     */
    public function Header(): void {
        // Faixa superior do cabeçalho (Primary Color #022448)
        $this->SetFillColor(2, 36, 72);
        $this->Rect(12, 10, 186, 14, 'F');

        // Título da Aplicação no lado esquerdo
        $this->SetFont('Helvetica', 'B', 14);
        $this->SetTextColor(255, 255, 255);
        $this->SetXY(16, 12);
        $this->Cell(80, 10, $this->encodeText(APP_NAME . ' — Gestão Financeira'), 0, 0, 'L');

        // Data e hora de geração no lado direito
        $this->SetFont('Helvetica', '', 8);
        $this->SetXY(110, 12);
        $this->Cell(84, 10, $this->encodeText('Gerado em: ' . date('d/m/Y H:i')), 0, 0, 'R');

        // Espaçador
        $this->Ln(16);

        // Título do Relatório
        $this->SetFont('Helvetica', 'B', 12);
        $this->SetTextColor(2, 36, 72);
        $this->Cell(186, 6, $this->encodeText($this->tituloRelatorio), 0, 1, 'L');

        // Subtítulo de Período e Filtros
        $this->SetFont('Helvetica', '', 9);
        $this->SetTextColor(67, 71, 78);
        
        $infoSubtitulo = 'Período: ' . $this->periodoTexto;
        if (!empty($this->filtrosAtivos)) {
            $infoSubtitulo .= ' | Filtros: ' . implode(', ', $this->filtrosAtivos);
        }
        $this->Cell(186, 5, $this->encodeText($infoSubtitulo), 0, 1, 'L');

        // Linha divisória
        $this->SetDrawColor(196, 198, 207);
        $this->SetLineWidth(0.3);
        $this->Line(12, $this->GetY() + 2, 198, $this->GetY() + 2);
        $this->Ln(5);
    }

    /**
     * Renderiza o rodapé padrão em todas as páginas do PDF.
     */
    public function Footer(): void {
        $this->SetY(-15);
        $this->SetDrawColor(227, 226, 230);
        $this->SetLineWidth(0.2);
        $this->Line(12, $this->GetY(), 198, $this->GetY());
        $this->Ln(2);

        $this->SetFont('Helvetica', '', 8);
        $this->SetTextColor(116, 119, 127);
        
        // Esquerda: Nome do Sistema
        $this->Cell(93, 8, $this->encodeText('Finzy — Sistema de Gestão Financeira (Relatório Oficial)'), 0, 0, 'L');
        
        // Direita: Numeração de Página
        $this->Cell(93, 8, $this->encodeText('Página ' . $this->PageNo() . ' de {nb}'), 0, 0, 'R');
    }

    /**
     * Desenha um card de resumo de totalizadores no topo do relatório.
     * 
     * @param array $totalizadores Array associativo ['Título' => 'Valor']
     */
    public function RenderSummaryBox(array $totalizadores): void {
        if (empty($totalizadores)) {
            return;
        }

        $count = count($totalizadores);
        $width = (186 - (($count - 1) * 3)) / $count;

        $this->SetFont('Helvetica', 'B', 8);
        
        $startX = 12;
        $startY = $this->GetY();

        foreach ($totalizadores as $label => $valor) {
            // Container do Card
            $this->SetFillColor(244, 243, 247); // surface-container-low
            $this->SetDrawColor(227, 226, 230);
            $this->Rect($startX, $startY, $width, 14, 'DF');

            // Rótulo
            $this->SetXY($startX + 2, $startY + 2);
            $this->SetFont('Helvetica', 'B', 7);
            $this->SetTextColor(67, 71, 78);
            $this->Cell($width - 4, 3, $this->encodeText(strtoupper($label)), 0, 1, 'L');

            // Valor
            $this->SetXY($startX + 2, $startY + 6);
            $this->SetFont('Helvetica', 'B', 9);
            $this->SetTextColor(2, 36, 72);
            $this->Cell($width - 4, 5, $this->encodeText($valor), 0, 1, 'L');

            $startX += $width + 3;
        }

        $this->SetY($startY + 17);
    }
}
