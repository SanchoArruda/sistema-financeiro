<?php
/**
 * Finzy — Controller de Categorias (CategoriaController)
 * 
 * Gerencia as requisições de listagem, cadastro, edição e alternância de status de categorias.
 * Restrito ao perfil de Administrador (RBAC).
 */

if (!defined('FINZY_BOOTSTRAP')) {
    http_response_code(403);
    exit('Acesso proibido.');
}

require_once __DIR__ . '/../helpers/AuthHelper.php';
require_once __DIR__ . '/../helpers/FormatHelper.php';
require_once __DIR__ . '/../helpers/LogHelper.php';
require_once __DIR__ . '/../models/CategoriaModel.php';

class CategoriaController {
    private CategoriaModel $categoriaModel;

    public function __construct() {
        $this->categoriaModel = new CategoriaModel();
    }

    /**
     * Exibe a listagem de categorias com filtros e formulário/modal de cadastro.
     */
    public function index(?string $erro = null, ?string $sucesso = null): void {
        AuthHelper::requireAdmin();

        $filtros = [
            'busca'  => trim($_GET['busca'] ?? ''),
            'tipo'   => trim($_GET['tipo'] ?? ''),
            'status' => trim($_GET['status'] ?? '')
        ];

        $categorias = $this->categoriaModel->listar($filtros);
        $csrfToken = AuthHelper::generateCsrfToken();

        require __DIR__ . '/../views/categorias/index.php';
    }

    /**
     * Processa a inserção ou atualização de uma categoria via POST.
     */
    public function salvar(): void {
        AuthHelper::requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?route=categorias');
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
        $status = trim($_POST['status'] ?? 'ativo');

        if (empty($nome)) {
            $this->index('O nome da categoria é obrigatório.');
            return;
        }

        if (!in_array($tipo, ['receita', 'despesa'], true)) {
            $this->index('O tipo de categoria informado é inválido. Selecione Receita ou Despesa.');
            return;
        }

        if (!in_array($status, ['ativo', 'inativo'], true)) {
            $status = 'ativo';
        }

        // Verifica duplicidade de nome para o mesmo tipo
        if ($this->categoriaModel->nomeExiste($nome, $tipo, $id)) {
            $this->index("Já existe uma categoria de " . ($tipo === 'receita' ? 'Receita' : 'Despesa') . " cadastrada com o nome \"{$nome}\".");
            return;
        }

        $usuarioLogado = AuthHelper::getLoggedUser();

        if ($id) {
            // Edição
            $sucesso = $this->categoriaModel->atualizar($id, [
                'nome' => $nome,
                'tipo' => $tipo,
                'status' => $status,
                'alterado_por' => $usuarioLogado['id']
            ]);

            if ($sucesso) {
                LogHelper::logSecurity('categoria_atualizada', ['id' => $id, 'nome' => $nome, 'usuario_id' => $usuarioLogado['id']]);
                header('Location: ?route=categorias&sucesso=atualizado');
                exit;
            } else {
                $this->index('Ocorreu um erro ao atualizar a categoria.');
            }
        } else {
            // Criação
            $novoId = $this->categoriaModel->criar([
                'nome' => $nome,
                'tipo' => $tipo,
                'status' => $status,
                'criado_por' => $usuarioLogado['id']
            ]);

            if ($novoId > 0) {
                LogHelper::logSecurity('categoria_criada', ['id' => $novoId, 'nome' => $nome, 'usuario_id' => $usuarioLogado['id']]);
                header('Location: ?route=categorias&sucesso=criado');
                exit;
            } else {
                $this->index('Ocorreu um erro ao cadastrar a categoria.');
            }
        }
    }

    /**
     * Alterna o status (ativo/inativo) de uma categoria via POST.
     */
    public function alternarStatus(): void {
        AuthHelper::requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?route=categorias');
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
        $sucesso = $this->categoriaModel->alternarStatus($id, $novoStatus, $usuarioLogado['id']);

        if ($sucesso) {
            LogHelper::logSecurity('categoria_status_alterado', ['id' => $id, 'novo_status' => $novoStatus, 'usuario_id' => $usuarioLogado['id']]);
            header('Location: ?route=categorias&sucesso=status');
            exit;
        } else {
            $this->index('Ocorreu um erro ao alterar o status da categoria.');
        }
    }
}
