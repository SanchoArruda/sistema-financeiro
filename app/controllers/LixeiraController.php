<?php
/**
 * Finzy — Controller da Lixeira (LixeiraController)
 * 
 * Gerencia a consulta e restauração de lançamentos financeiros excluídos logicamente,
 * com diferenciação de permissão por perfil (Administrador vs Operador).
 */

if (!defined('FINZY_BOOTSTRAP')) {
    http_response_code(403);
    exit('Acesso proibido.');
}

require_once __DIR__ . '/../models/LancamentoModel.php';
require_once __DIR__ . '/../helpers/AuthHelper.php';
require_once __DIR__ . '/../helpers/LogHelper.php';

class LixeiraController {

    private LancamentoModel $lancamentoModel;

    public function __construct() {
        AuthHelper::requireLogin();
        $this->lancamentoModel = new LancamentoModel();
    }

    /**
     * Exibe a listagem de itens da Lixeira.
     * Operador visualiza apenas os itens excluídos por ele próprio.
     * Administrador visualiza todos os itens excluídos.
     */
    public function index(): void {
        $usuarioLogado = AuthHelper::getLoggedUser();
        $isAdmin = AuthHelper::isAdmin();

        $pagina = isset($_GET['pagina']) ? max(1, (int)$_GET['pagina']) : 1;
        $busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';
        $porPagina = 20;

        $filtros = [];

        if (!empty($busca)) {
            $filtros['busca'] = $busca;
        }

        // Operador visualiza apenas os registros que ele próprio excluiu
        if (!$isAdmin) {
            $filtros['excluido_por'] = (int)$usuarioLogado['id'];
        }

        $itens = $this->lancamentoModel->listarLixeira($filtros, $pagina, $porPagina);
        $totalItens = $this->lancamentoModel->contarLixeira($filtros);
        $totalPaginas = (int) ceil($totalItens / $porPagina);

        $tituloPagina = 'Lixeira — ' . (defined('APP_NAME') ? APP_NAME : 'Finzy');

        require __DIR__ . '/../views/lixeira/index.php';
    }

    /**
     * Processa a restauração de um item excluído.
     */
    public function restaurar(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?route=lixeira');
            exit;
        }

        AuthHelper::validateCsrfToken();

        $usuarioLogado = AuthHelper::getLoggedUser();
        $usuarioId = (int)$usuarioLogado['id'];
        $isAdmin = AuthHelper::isAdmin();

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

        if ($id <= 0) {
            $_SESSION['mensagem_erro'] = 'Lançamento inválido para restauração.';
            header('Location: ?route=lixeira');
            exit;
        }

        $item = $this->lancamentoModel->buscarExcluidoPorId($id);

        if (!$item) {
            $_SESSION['mensagem_erro'] = 'Lançamento não foi encontrado na lixeira.';
            header('Location: ?route=lixeira');
            exit;
        }

        // Validação RBAC: Operador só pode restaurar o que ele próprio excluiu
        if (!$isAdmin && (int)$item['excluido_por'] !== $usuarioId) {
            LogHelper::logSecurity('Tentativa de restauração não autorizada na lixeira', [
                'usuario_id' => $usuarioId,
                'lancamento_id' => $id,
                'excluido_por_original' => $item['excluido_por']
            ]);

            $_SESSION['mensagem_erro'] = 'Você não tem permissão para restaurar lançamentos excluídos por outros usuários.';
            header('Location: ?route=lixeira');
            exit;
        }

        // Restaura o item
        $sucesso = $this->lancamentoModel->restaurar($id);

        if ($sucesso) {
            LogHelper::logSecurity('Restauração de item da Lixeira', [
                'usuario_id' => $usuarioId,
                'lancamento_id' => $id,
                'descricao' => $item['descricao'],
                'valor' => $item['valor']
            ]);

            $_SESSION['mensagem_sucesso'] = 'Lançamento "' . htmlspecialchars($item['descricao'], ENT_QUOTES, 'UTF-8') . '" restaurado com sucesso!';
        } else {
            $_SESSION['mensagem_erro'] = 'Não foi possível restaurar o lançamento. Tente novamente.';
        }

        header('Location: ?route=lixeira');
        exit;
    }
}
