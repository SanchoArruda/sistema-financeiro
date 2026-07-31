<?php
/**
 * Finzy — Controller de Contas Financeiras (ContaController)
 * 
 * Gerencia as requisições de listagem, cadastro, edição e alternância de status de contas financeiras.
 * Restrito ao perfil de Administrador (RBAC).
 */

if (!defined('FINZY_BOOTSTRAP')) {
    http_response_code(403);
    exit('Acesso proibido.');
}

require_once __DIR__ . '/../helpers/AuthHelper.php';
require_once __DIR__ . '/../helpers/FormatHelper.php';
require_once __DIR__ . '/../helpers/LogHelper.php';
require_once __DIR__ . '/../models/ContaModel.php';

class ContaController {
    private ContaModel $contaModel;

    public function __construct() {
        $this->contaModel = new ContaModel();
    }

    /**
     * Exibe a listagem de contas financeiras com filtros, cálculo de saldo atual e modal de cadastro.
     */
    public function index(?string $erro = null, ?string $sucesso = null): void {
        AuthHelper::requireAdmin();

        $filtros = [
            'busca'  => trim($_GET['busca'] ?? ''),
            'tipo'   => trim($_GET['tipo'] ?? ''),
            'status' => trim($_GET['status'] ?? '')
        ];

        $contas = $this->contaModel->listar($filtros);
        $tiposConta = ContaModel::TIPOS_CONTA;
        $csrfToken = AuthHelper::generateCsrfToken();

        require __DIR__ . '/../views/contas/index.php';
    }

    /**
     * Processa a inserção ou atualização de uma conta financeira via POST.
     */
    public function salvar(): void {
        AuthHelper::requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?route=contas');
            exit;
        }

        $tokenCsrf = $_POST['csrf_token'] ?? '';
        if (!AuthHelper::validateCsrfToken($tokenCsrf)) {
            $this->index('Sessão ou formulário inválido. Por favor, tente novamente.');
            return;
        }

        $id = !empty($_POST['id']) ? (int) $_POST['id'] : null;
        $nome = trim($_POST['nome'] ?? '');
        $tipo = trim($_POST['tipo'] ?? '');
        $saldoInicialRaw = $_POST['saldo_inicial'] ?? '0';
        $status = trim($_POST['status'] ?? 'ativo');

        if (empty($nome)) {
            $this->index('O nome da conta é obrigatório.');
            return;
        }

        if (!array_key_exists($tipo, ContaModel::TIPOS_CONTA)) {
            $this->index('O tipo de conta selecionado é inválido.');
            return;
        }

        if (!in_array($status, ['ativo', 'inativo'], true)) {
            $status = 'ativo';
        }

        $saldoInicial = FormatHelper::parseMoeda($saldoInicialRaw);

        // Verifica duplicidade de nome
        if ($this->contaModel->nomeExiste($nome, $id)) {
            $this->index("Já existe uma conta financeira cadastrada com o nome \"{$nome}\".");
            return;
        }

        $usuarioLogado = AuthHelper::getLoggedUser();

        if ($id) {
            // Edição
            $sucesso = $this->contaModel->atualizar($id, [
                'nome' => $nome,
                'tipo' => $tipo,
                'saldo_inicial' => $saldoInicial,
                'status' => $status,
                'alterado_por' => $usuarioLogado['id']
            ]);

            if ($sucesso) {
                LogHelper::logSecurity('conta_atualizada', ['id' => $id, 'nome' => $nome, 'usuario_id' => $usuarioLogado['id']]);
                header('Location: ?route=contas&sucesso=atualizado');
                exit;
            } else {
                $this->index('Ocorreu um erro ao atualizar a conta financeira.');
            }
        } else {
            // Criação
            $novoId = $this->contaModel->criar([
                'nome' => $nome,
                'tipo' => $tipo,
                'saldo_inicial' => $saldoInicial,
                'status' => $status,
                'criado_por' => $usuarioLogado['id']
            ]);

            if ($novoId > 0) {
                LogHelper::logSecurity('conta_criada', ['id' => $novoId, 'nome' => $nome, 'usuario_id' => $usuarioLogado['id']]);
                header('Location: ?route=contas&sucesso=criado');
                exit;
            } else {
                $this->index('Ocorreu um erro ao cadastrar a conta financeira.');
            }
        }
    }

    /**
     * Alterna o status (ativo/inativo) de uma conta financeira via POST.
     */
    public function alternarStatus(): void {
        AuthHelper::requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?route=contas');
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
        $sucesso = $this->contaModel->alternarStatus($id, $novoStatus, $usuarioLogado['id']);

        if ($sucesso) {
            LogHelper::logSecurity('conta_status_alterado', ['id' => $id, 'novo_status' => $novoStatus, 'usuario_id' => $usuarioLogado['id']]);
            header('Location: ?route=contas&sucesso=status');
            exit;
        } else {
            $this->index('Ocorreu um erro ao alterar o status da conta financeira.');
        }
    }
}
