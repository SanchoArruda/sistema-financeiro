<?php
/**
 * Finzy — Controller de Formas de Pagamento (FormaPagamentoController)
 * 
 * Gerencia as requisições de listagem, cadastro, edição e alternância de status de formas de pagamento.
 * Restrito ao perfil de Administrador (RBAC).
 */

if (!defined('FINZY_BOOTSTRAP')) {
    http_response_code(403);
    exit('Acesso proibido.');
}

require_once __DIR__ . '/../helpers/AuthHelper.php';
require_once __DIR__ . '/../helpers/FormatHelper.php';
require_once __DIR__ . '/../helpers/LogHelper.php';
require_once __DIR__ . '/../models/FormaPagamentoModel.php';

class FormaPagamentoController {
    private FormaPagamentoModel $formaPagamentoModel;

    public function __construct() {
        $this->formaPagamentoModel = new FormaPagamentoModel();
    }

    /**
     * Exibe a listagem de formas de pagamento com filtros e modal de cadastro.
     */
    public function index(?string $erro = null, ?string $sucesso = null): void {
        AuthHelper::requireAdmin();

        $filtros = [
            'busca'  => trim($_GET['busca'] ?? ''),
            'status' => trim($_GET['status'] ?? '')
        ];

        $formasPagamento = $this->formaPagamentoModel->listar($filtros);
        $csrfToken = AuthHelper::generateCsrfToken();

        require __DIR__ . '/../views/formas_pagamento/index.php';
    }

    /**
     * Processa a inserção ou atualização de uma forma de pagamento via POST.
     */
    public function salvar(): void {
        AuthHelper::requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?route=formas_pagamento');
            exit;
        }

        $tokenCsrf = $_POST['csrf_token'] ?? '';
        if (!AuthHelper::validateCsrfToken($tokenCsrf)) {
            $this->index('Sessão ou formulário inválido. Por favor, tente novamente.');
            return;
        }

        $id = !empty($_POST['id']) ? (int) $_POST['id'] : null;
        $nome = trim($_POST['nome'] ?? '');
        $status = trim($_POST['status'] ?? 'ativo');

        if (empty($nome)) {
            $this->index('O nome da forma de pagamento é obrigatório.');
            return;
        }

        if (!in_array($status, ['ativo', 'inativo'], true)) {
            $status = 'ativo';
        }

        // Verifica duplicidade de nome
        if ($this->formaPagamentoModel->nomeExiste($nome, $id)) {
            $this->index("Já existe uma forma de pagamento cadastrada com o nome \"{$nome}\".");
            return;
        }

        $usuarioLogado = AuthHelper::getLoggedUser();

        if ($id) {
            // Edição
            $sucesso = $this->formaPagamentoModel->atualizar($id, [
                'nome' => $nome,
                'status' => $status,
                'alterado_por' => $usuarioLogado['id']
            ]);

            if ($sucesso) {
                LogHelper::logSecurity('forma_pagamento_atualizada', ['id' => $id, 'nome' => $nome, 'usuario_id' => $usuarioLogado['id']]);
                header('Location: ?route=formas_pagamento&sucesso=atualizado');
                exit;
            } else {
                $this->index('Ocorreu um erro ao atualizar a forma de pagamento.');
            }
        } else {
            // Criação
            $novoId = $this->formaPagamentoModel->criar([
                'nome' => $nome,
                'status' => $status,
                'criado_por' => $usuarioLogado['id']
            ]);

            if ($novoId > 0) {
                LogHelper::logSecurity('forma_pagamento_criada', ['id' => $novoId, 'nome' => $nome, 'usuario_id' => $usuarioLogado['id']]);
                header('Location: ?route=formas_pagamento&sucesso=criado');
                exit;
            } else {
                $this->index('Ocorreu um erro ao cadastrar a forma de pagamento.');
            }
        }
    }

    /**
     * Alterna o status (ativo/inativo) de uma forma de pagamento via POST.
     */
    public function alternarStatus(): void {
        AuthHelper::requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?route=formas_pagamento');
            exit;
        }

        $tokenCsrf = $_POST['csrf_token'] ?? '';
        if (!AuthHelper::validateCsrfToken($tokenCsrf)) {
            $this->index('Sessão ou formulário inválido.');
            return;
        }

        $id = (int) ($_POST['id'] ?? 0);
        $novoStatus = trim($_POST['status'] ?? '');

        if ($id <= 0 || !in_array($novoStatus, ['ativo', 'inativo'], true)) {
            $this->index('Parâmetros inválidos para alteração de status.');
            return;
        }

        $usuarioLogado = AuthHelper::getLoggedUser();
        $sucesso = $this->formaPagamentoModel->alternarStatus($id, $novoStatus, $usuarioLogado['id']);

        if ($sucesso) {
            LogHelper::logSecurity('forma_pagamento_status_alterado', ['id' => $id, 'novo_status' => $novoStatus, 'usuario_id' => $usuarioLogado['id']]);
            header('Location: ?route=formas_pagamento&sucesso=status');
            exit;
        } else {
            $this->index('Ocorreu um erro ao alterar o status da forma de pagamento.');
        }
    }
}
