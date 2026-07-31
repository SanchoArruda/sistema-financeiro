<?php
/**
 * Finzy — Controller de Lançamentos Financeiros (LancamentoController)
 * 
 * Gerencia as requisições de listagem, criação, edição e exclusão lógica de lançamentos.
 * Acessível por Administradores e Operadores (RBAC).
 */

if (!defined('FINZY_BOOTSTRAP')) {
    http_response_code(403);
    exit('Acesso proibido.');
}

require_once __DIR__ . '/../models/LancamentoModel.php';
require_once __DIR__ . '/../models/CategoriaModel.php';
require_once __DIR__ . '/../models/ContaModel.php';
require_once __DIR__ . '/../models/FormaPagamentoModel.php';
require_once __DIR__ . '/../models/UsuarioModel.php';
require_once __DIR__ . '/../helpers/AuthHelper.php';
require_once __DIR__ . '/../helpers/FormatHelper.php';
require_once __DIR__ . '/../helpers/LogHelper.php';

class LancamentoController {

    private LancamentoModel $lancamentoModel;
    private CategoriaModel $categoriaModel;
    private ContaModel $contaModel;
    private FormaPagamentoModel $formaPagamentoModel;
    private UsuarioModel $usuarioModel;

    public function __construct() {
        AuthHelper::requireRole(['administrador', 'operador']);
        $this->lancamentoModel     = new LancamentoModel();
        $this->categoriaModel      = new CategoriaModel();
        $this->contaModel          = new ContaModel();
        $this->formaPagamentoModel = new FormaPagamentoModel();
        $this->usuarioModel        = new UsuarioModel();
    }

    /**
     * Exibe a listagem de lançamentos com busca, filtros e paginação.
     */
    public function index(): void {
        $filtros = [
            'busca'              => trim($_GET['busca'] ?? ''),
            'tipo'               => trim($_GET['tipo'] ?? ''),
            'categoria_id'       => (int)($_GET['categoria_id'] ?? 0),
            'conta_id'           => (int)($_GET['conta_id'] ?? 0),
            'forma_pagamento_id' => (int)($_GET['forma_pagamento_id'] ?? 0),
            'situacao'           => trim($_GET['situacao'] ?? ''),
            'criado_por'         => (int)($_GET['criado_por'] ?? 0),
            'data_inicio'        => trim($_GET['data_inicio'] ?? ''),
            'data_fim'           => trim($_GET['data_fim'] ?? '')
        ];

        $paginaAtual = max(1, (int)($_GET['pagina'] ?? 1));
        $porPagina = 20;

        $lancamentos = $this->lancamentoModel->listar($filtros, $paginaAtual, $porPagina);
        $totalRegistros = $this->lancamentoModel->contar($filtros);
        $totaisFiltro = $this->lancamentoModel->obterTotaisFiltro($filtros);
        $totalPaginas = max(1, (int)ceil($totalRegistros / $porPagina));

        // Dados para selects de filtros
        $categorias = $this->categoriaModel->listar();
        $contas = $this->contaModel->listar();
        $formasPagamento = $this->formaPagamentoModel->listar();
        $usuarios = $this->usuarioModel->listar();

        $tituloPagina = 'Lançamentos Financeiros — ' . APP_NAME;

        require __DIR__ . '/../views/lancamentos/index.php';
    }

    /**
     * Exibe o formulário de cadastro de novo lançamento.
     */
    public function novo(): void {
        $lancamento = [
            'id'                 => 0,
            'tipo'               => $_GET['tipo'] ?? 'despesa',
            'descricao'          => '',
            'valor'              => '',
            'data_lancamento'    => date('Y-m-d'),
            'data_pagamento'     => '',
            'categoria_id'       => 0,
            'conta_id'           => 0,
            'forma_pagamento_id' => 0
        ];

        // Se houver dados em rascunho de erro na sessão
        if (!empty($_SESSION['form_data'])) {
            $lancamento = array_merge($lancamento, $_SESSION['form_data']);
            unset($_SESSION['form_data']);
        }

        // Seleciona opções ativas para novos cadastros
        $categorias = $this->categoriaModel->listar(['status' => 'ativo']);
        $contas = $this->contaModel->listar(['status' => 'ativo']);
        $formasPagamento = $this->formaPagamentoModel->listar(['status' => 'ativo']);

        $modoEdicao = false;
        $tituloPagina = 'Novo Lançamento — ' . APP_NAME;

        require __DIR__ . '/../views/lancamentos/form.php';
    }

    /**
     * Exibe o formulário de edição de um lançamento existente.
     */
    public function editar(): void {
        $id = (int)($_GET['id'] ?? 0);
        $lancamento = $this->lancamentoModel->buscarPorId($id);

        if (!$lancamento) {
            $_SESSION['erro'] = 'Lançamento não encontrado ou já excluído.';
            header('Location: ?route=lancamentos');
            exit;
        }

        // Se houver dados em rascunho de erro na sessão
        if (!empty($_SESSION['form_data'])) {
            $lancamento = array_merge($lancamento, $_SESSION['form_data']);
            unset($_SESSION['form_data']);
        }

        // Lista todas as categorias, contas e formas de pagamento
        // (RN05: preserva itens inativos vinculados ao lançamento atual para visualização)
        $todasCategorias = $this->categoriaModel->listar();
        $todasContas = $this->contaModel->listar();
        $todasFormas = $this->formaPagamentoModel->listar();

        $categorias = array_filter($todasCategorias, function($c) use ($lancamento) {
            return $c['status'] === 'ativo' || (int)$c['id'] === (int)$lancamento['categoria_id'];
        });

        $contas = array_filter($todasContas, function($c) use ($lancamento) {
            return $c['status'] === 'ativo' || (int)$c['id'] === (int)$lancamento['conta_id'];
        });

        $formasPagamento = array_filter($todasFormas, function($f) use ($lancamento) {
            return $f['status'] === 'ativo' || (int)$f['id'] === (int)$lancamento['forma_pagamento_id'];
        });

        $modoEdicao = true;
        $tituloPagina = 'Editar Lançamento — ' . APP_NAME;

        require __DIR__ . '/../views/lancamentos/form.php';
    }

    /**
     * Processa a gravação (criação ou edição) de um lançamento via POST.
     */
    public function salvar(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?route=lancamentos');
            exit;
        }

        if (!AuthHelper::validateCsrfToken($_POST['csrf_token'] ?? null)) {
            $_SESSION['erro'] = 'Token de segurança inválido. Tente novamente.';
            header('Location: ?route=lancamentos');
            exit;
        }

        $usuarioLogado = AuthHelper::getLoggedUser();
        $id = (int)($_POST['id'] ?? 0);
        $tipo = trim($_POST['tipo'] ?? 'despesa');
        $descricao = trim($_POST['descricao'] ?? '');
        $valorRaw = $_POST['valor'] ?? '0';
        $valor = FormatHelper::parseMoeda($valorRaw);
        $dataLancamento = trim($_POST['data_lancamento'] ?? '');
        $dataPagamento = trim($_POST['data_pagamento'] ?? '');
        $categoriaId = (int)($_POST['categoria_id'] ?? 0);
        $contaId = (int)($_POST['conta_id'] ?? 0);
        $formaPagamentoId = (int)($_POST['forma_pagamento_id'] ?? 0);

        $erros = [];

        // Validação dos campos obrigatórios
        if (!in_array($tipo, ['receita', 'despesa'], true)) {
            $erros[] = 'Selecione um tipo válido de lançamento (Receita ou Despesa).';
        }

        if (empty($descricao)) {
            $erros[] = 'A descrição do lançamento é obrigatória.';
        } elseif (mb_strlen($descricao) > 255) {
            $erros[] = 'A descrição não pode ter mais de 255 caracteres.';
        }

        if ($valor <= 0) {
            $erros[] = 'O valor do lançamento deve ser maior que zero (ex: R$ 50,00).';
        }

        if (empty($dataLancamento) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dataLancamento)) {
            $erros[] = 'Informe uma data de lançamento válida.';
        }

        if (!empty($dataPagamento) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dataPagamento)) {
            $erros[] = 'Informe uma data de pagamento/recebimento válida.';
        }

        // Validação da Categoria
        if ($categoriaId <= 0) {
            $erros[] = 'Selecione uma categoria para o lançamento.';
        } else {
            $cat = $this->categoriaModel->buscarPorId($categoriaId);
            if (!$cat) {
                $erros[] = 'A categoria selecionada não foi encontrada.';
            } elseif ($cat['tipo'] !== $tipo) {
                $erros[] = "A categoria '{$cat['nome']}' é do tipo {$cat['tipo']} e incompatível com este lançamento do tipo {$tipo}.";
            }
        }

        // Validação da Conta
        if ($contaId <= 0) {
            $erros[] = 'Selecione uma conta financeira.';
        } else {
            $conta = $this->contaModel->buscarPorId($contaId);
            if (!$conta) {
                $erros[] = 'A conta financeira selecionada não foi encontrada.';
            }
        }

        // Validação da Forma de Pagamento
        if ($formaPagamentoId <= 0) {
            $erros[] = 'Selecione uma forma de pagamento.';
        } else {
            $fpg = $this->formaPagamentoModel->buscarPorId($formaPagamentoId);
            if (!$fpg) {
                $erros[] = 'A forma de pagamento selecionada não foi encontrada.';
            }
        }

        // Se houver erros de validação
        if (!empty($erros)) {
            $_SESSION['erro'] = implode('<br>', $erros);
            $_SESSION['form_data'] = $_POST;
            $redirect = $id > 0 ? "?route=lancamentos_editar&id={$id}" : "?route=lancamentos_novo";
            header("Location: {$redirect}");
            exit;
        }

        $dadosLancamento = [
            'tipo'               => $tipo,
            'descricao'          => $descricao,
            'valor'              => $valor,
            'data_lancamento'    => $dataLancamento,
            'data_pagamento'     => !empty($dataPagamento) ? $dataPagamento : null,
            'categoria_id'       => $categoriaId,
            'conta_id'           => $contaId,
            'forma_pagamento_id' => $formaPagamentoId
        ];

        if ($id > 0) {
            // Edição
            $existente = $this->lancamentoModel->buscarPorId($id);
            if (!$existente) {
                $_SESSION['erro'] = 'Lançamento não encontrado ou já excluído.';
                header('Location: ?route=lancamentos');
                exit;
            }

            $dadosLancamento['alterado_por'] = $usuarioLogado['id'];
            $sucesso = $this->lancamentoModel->atualizar($id, $dadosLancamento);

            if ($sucesso) {
                LogHelper::logSecurity('edicao_lancamento', [
                    'id'          => $id,
                    'descricao'   => $descricao,
                    'valor'       => $valor,
                    'alterado_por'=> $usuarioLogado['id']
                ]);
                $_SESSION['sucesso'] = "Lançamento '{$descricao}' atualizado com sucesso!";
            } else {
                $_SESSION['erro'] = "Erro ao atualizar o lançamento. Tente novamente.";
            }
        } else {
            // Cadastro
            $dadosLancamento['criado_por'] = $usuarioLogado['id'];
            $novoId = $this->lancamentoModel->criar($dadosLancamento);

            if ($novoId > 0) {
                LogHelper::logSecurity('criacao_lancamento', [
                    'id'         => $novoId,
                    'descricao'  => $descricao,
                    'valor'      => $valor,
                    'criado_por' => $usuarioLogado['id']
                ]);
                $_SESSION['sucesso'] = "Lançamento '{$descricao}' cadastrado com sucesso!";
            } else {
                $_SESSION['erro'] = "Erro ao cadastrar o lançamento. Tente novamente.";
            }
        }

        header('Location: ?route=lancamentos');
        exit;
    }

    /**
     * Processa a exclusão lógica (soft delete) de um lançamento via POST.
     */
    public function excluir(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?route=lancamentos');
            exit;
        }

        if (!AuthHelper::validateCsrfToken($_POST['csrf_token'] ?? null)) {
            $_SESSION['erro'] = 'Token de segurança inválido. Tente novamente.';
            header('Location: ?route=lancamentos');
            exit;
        }

        $usuarioLogado = AuthHelper::getLoggedUser();
        $id = (int)($_POST['id'] ?? 0);

        $lancamento = $this->lancamentoModel->buscarPorId($id);

        if (!$lancamento) {
            $_SESSION['erro'] = 'Lançamento não encontrado ou já excluído.';
            header('Location: ?route=lancamentos');
            exit;
        }

        $sucesso = $this->lancamentoModel->softDelete($id, $usuarioLogado['id']);

        if ($sucesso) {
            LogHelper::logSecurity('exclusao_lancamento', [
                'id'           => $id,
                'descricao'    => $lancamento['descricao'],
                'valor'        => $lancamento['valor'],
                'excluido_por' => $usuarioLogado['id']
            ]);
            $_SESSION['sucesso'] = "Lançamento '{$lancamento['descricao']}' removido com sucesso para a Lixeira!";
        } else {
            $_SESSION['erro'] = "Erro ao excluir o lançamento. Tente novamente.";
        }

        header('Location: ?route=lancamentos');
        exit;
    }
}
