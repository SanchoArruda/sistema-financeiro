<?php
/**
 * Finzy — Controller de Usuários (UsuarioController)
 * 
 * Gerencia as requisições de administração de usuários (restrito a Administradores)
 * e a auto-gestão de perfil (acessível por qualquer usuário autenticado).
 */

if (!defined('FINZY_BOOTSTRAP')) {
    http_response_code(403);
    exit('Acesso proibido.');
}

require_once __DIR__ . '/../helpers/AuthHelper.php';
require_once __DIR__ . '/../helpers/FormatHelper.php';
require_once __DIR__ . '/../helpers/LogHelper.php';
require_once __DIR__ . '/../models/UsuarioModel.php';

class UsuarioController {
    private UsuarioModel $usuarioModel;

    public function __construct() {
        $this->usuarioModel = new UsuarioModel();
    }

    /**
     * Exibe a listagem de usuários com filtros e formulário/modal de cadastro (Administrativo).
     */
    public function index(?string $erro = null, ?string $sucesso = null): void {
        AuthHelper::requireAdmin();

        $filtros = [
            'busca'  => trim($_GET['busca'] ?? ''),
            'perfil' => trim($_GET['perfil'] ?? ''),
            'status' => trim($_GET['status'] ?? '')
        ];

        $usuarios = $this->usuarioModel->listar($filtros);
        $csrfToken = AuthHelper::generateCsrfToken();
        $usuarioLogado = AuthHelper::getLoggedUser();

        require __DIR__ . '/../views/usuarios/index.php';
    }

    /**
     * Processa o cadastro ou edição de usuário pelo Administrador.
     */
    public function salvar(): void {
        AuthHelper::requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?route=usuarios');
            exit;
        }

        $tokenCsrf = $_POST['csrf_token'] ?? '';
        if (!AuthHelper::validateCsrfToken($tokenCsrf)) {
            $this->index('Sessão ou formulário inválido. Por favor, tente novamente.');
            return;
        }

        $usuarioLogado = AuthHelper::getLoggedUser();
        $id = !empty($_POST['id']) ? (int) $_POST['id'] : null;
        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $perfil = trim($_POST['perfil'] ?? '');
        $status = trim($_POST['status'] ?? 'ativo');
        $senha = $_POST['senha'] ?? '';

        // Validações básicas
        if (mb_strlen($nome, 'UTF-8') < 3 || mb_strlen($nome, 'UTF-8') > 150) {
            $this->index('O nome deve conter entre 3 e 150 caracteres.');
            return;
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->index('Informe um endereço de e-mail válido.');
            return;
        }

        if (!in_array($perfil, ['administrador', 'operador'], true)) {
            $this->index('Selecione um perfil válido (Administrador ou Operador).');
            return;
        }

        if (!in_array($status, ['ativo', 'inativo'], true)) {
            $status = 'ativo';
        }

        // Validação de unicidade de e-mail
        if ($this->usuarioModel->emailExiste($email, $id)) {
            $this->index("O e-mail \"{$email}\" já está cadastrado para outro usuário.");
            return;
        }

        // Regra de segurança: Administrador não pode inativar a si mesmo
        if ($id === $usuarioLogado['id'] && $status === 'inativo') {
            LogHelper::logSecurity('tentativa_auto_inativacao', ['usuario_id' => $usuarioLogado['id']]);
            $this->index('Você não pode inativar sua própria conta de usuário.');
            return;
        }

        // Regra de segurança: Administrador não pode rebaixar a si mesmo para operador
        if ($id === $usuarioLogado['id'] && $perfil === 'operador') {
            LogHelper::logSecurity('tentativa_auto_rebaixamento', ['usuario_id' => $usuarioLogado['id']]);
            $this->index('Você não pode alterar seu próprio perfil de Administrador para Operador.');
            return;
        }

        if ($id) {
            // Edição
            $dadosUpdate = [
                'nome' => $nome,
                'email' => $email,
                'perfil' => $perfil,
                'status' => $status,
                'alterado_por' => $usuarioLogado['id']
            ];

            if (!empty($senha)) {
                if (mb_strlen($senha, 'UTF-8') < 8) {
                    $this->index('A nova senha informada deve conter no mínimo 8 caracteres.');
                    return;
                }
                $dadosUpdate['senha_hash'] = password_hash($senha, PASSWORD_DEFAULT);
            }

            $sucesso = $this->usuarioModel->atualizar($id, $dadosUpdate);

            if ($sucesso) {
                // Se editou a si mesmo, atualiza os dados na sessão ativa
                if ($id === $usuarioLogado['id']) {
                    $_SESSION['usuario_nome'] = $nome;
                    $_SESSION['usuario_email'] = $email;
                    $_SESSION['usuario_perfil'] = $perfil;
                }

                LogHelper::logSecurity('usuario_atualizado', ['id' => $id, 'nome' => $nome, 'admin_id' => $usuarioLogado['id']]);
                header('Location: ?route=usuarios&sucesso=atualizado');
                exit;
            } else {
                $this->index('Ocorreu um erro ao atualizar os dados do usuário.');
            }

        } else {
            // Criação
            if (empty($senha) || mb_strlen($senha, 'UTF-8') < 8) {
                $this->index('Para cadastrar um novo usuário, informe uma senha inicial com no mínimo 8 caracteres.');
                return;
            }

            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

            $novoId = $this->usuarioModel->criar([
                'nome' => $nome,
                'email' => $email,
                'senha_hash' => $senhaHash,
                'perfil' => $perfil,
                'status' => $status,
                'primeiro_acesso' => 1,
                'criado_por' => $usuarioLogado['id']
            ]);

            if ($novoId > 0) {
                LogHelper::logSecurity('usuario_criado', ['id' => $novoId, 'nome' => $nome, 'email' => $email, 'perfil' => $perfil, 'admin_id' => $usuarioLogado['id']]);
                header('Location: ?route=usuarios&sucesso=criado');
                exit;
            } else {
                $this->index('Ocorreu um erro ao cadastrar o novo usuário.');
            }
        }
    }

    /**
     * Alterna o status (ativo/inativo) de um usuário via POST (Administrativo).
     */
    public function alternarStatus(): void {
        AuthHelper::requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?route=usuarios');
            exit;
        }

        $tokenCsrf = $_POST['csrf_token'] ?? '';
        if (!AuthHelper::validateCsrfToken($tokenCsrf)) {
            $this->index('Sessão ou formulário inválido.');
            return;
        }

        $id = (int) ($_POST['id'] ?? 0);
        $novoStatus = trim($_POST['status'] ?? '');
        $usuarioLogado = AuthHelper::getLoggedUser();

        if ($id <= 0 || !in_array($novoStatus, ['ativo', 'inativo'], true)) {
            $this->index('Parâmetros inválidos para alteração de status.');
            return;
        }

        // Regra de segurança: Administrador não pode inativar a si mesmo
        if ($id === $usuarioLogado['id'] && $novoStatus === 'inativo') {
            LogHelper::logSecurity('tentativa_auto_inativacao', ['usuario_id' => $usuarioLogado['id']]);
            $this->index('Você não pode inativar sua própria conta de usuário.');
            return;
        }

        $sucesso = $this->usuarioModel->alternarStatus($id, $novoStatus, $usuarioLogado['id']);

        if ($sucesso) {
            LogHelper::logSecurity('usuario_inativado_ou_ativado', [
                'usuario_afetado' => $id,
                'novo_status' => $novoStatus,
                'admin_id' => $usuarioLogado['id']
            ]);
            header('Location: ?route=usuarios&sucesso=status');
            exit;
        } else {
            $this->index('Ocorreu um erro ao alterar o status do usuário.');
        }
    }

    /**
     * Exibe a tela de Auto-gestão de Perfil (Meu Perfil) para qualquer usuário logado.
     */
    public function meuPerfil(?string $erro = null, ?string $sucesso = null): void {
        AuthHelper::requireLogin();

        $usuarioLogado = AuthHelper::getLoggedUser();
        $usuarioCompleto = $this->usuarioModel->buscarPorId($usuarioLogado['id']);

        if (!$usuarioCompleto) {
            AuthHelper::logout();
            header('Location: ?route=login');
            exit;
        }

        $csrfToken = AuthHelper::generateCsrfToken();

        require __DIR__ . '/../views/usuarios/meu_perfil.php';
    }

    /**
     * Processa a atualização do perfil próprio (Nome e/ou Senha) via POST.
     */
    public function salvarMeuPerfil(): void {
        AuthHelper::requireLogin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?route=meu_perfil');
            exit;
        }

        $tokenCsrf = $_POST['csrf_token'] ?? '';
        if (!AuthHelper::validateCsrfToken($tokenCsrf)) {
            $this->meuPerfil('Sessão ou formulário inválido. Por favor, tente novamente.');
            return;
        }

        $usuarioLogado = AuthHelper::getLoggedUser();
        $usuarioBanco = $this->usuarioModel->buscarPorId($usuarioLogado['id']);

        if (!$usuarioBanco) {
            AuthHelper::logout();
            header('Location: ?route=login');
            exit;
        }

        $nome = trim($_POST['nome'] ?? '');
        $senhaAtual = $_POST['senha_atual'] ?? '';
        $novaSenha = $_POST['nova_senha'] ?? '';
        $confirmarNovaSenha = $_POST['confirmar_nova_senha'] ?? '';

        // Validação do nome
        if (mb_strlen($nome, 'UTF-8') < 3 || mb_strlen($nome, 'UTF-8') > 150) {
            $this->meuPerfil('O nome deve conter entre 3 e 150 caracteres.');
            return;
        }

        // Validação obrigatória da senha atual
        if (empty($senhaAtual)) {
            $this->meuPerfil('É necessário informar sua senha atual para confirmar as alterações.');
            return;
        }

        if (!password_verify($senhaAtual, $usuarioBanco['senha_hash'])) {
            LogHelper::logSecurity('perfil_senha_atual_invalida', ['usuario_id' => $usuarioLogado['id']]);
            $this->meuPerfil('A senha atual informada está incorreta.');
            return;
        }

        // Se informou nova senha, valida tamanho e confirmação
        $trocandoSenha = !empty($novaSenha);
        if ($trocandoSenha) {
            if (mb_strlen($novaSenha, 'UTF-8') < 8) {
                $this->meuPerfil('A nova senha deve conter no mínimo 8 caracteres.');
                return;
            }

            if ($novaSenha !== $confirmarNovaSenha) {
                $this->meuPerfil('A nova senha e a confirmação de senha não coincidem.');
                return;
            }
        }

        // Executa atualização no banco
        $sucesso = $this->usuarioModel->atualizarPerfilProprio(
            $usuarioLogado['id'],
            $nome,
            $trocandoSenha ? $novaSenha : null
        );

        if ($sucesso) {
            // Atualiza o nome na sessão ativa
            $_SESSION['usuario_nome'] = $nome;

            LogHelper::logSecurity('perfil_proprio_atualizado', [
                'usuario_id' => $usuarioLogado['id'],
                'alterou_senha' => $trocandoSenha
            ]);

            header('Location: ?route=meu_perfil&sucesso=1');
            exit;
        } else {
            $this->meuPerfil('Ocorreu um erro ao atualizar os dados do seu perfil.');
        }
    }
}
