<?php
/**
 * Finzy — Controller de Autenticação (AuthController)
 * 
 * Responsável por gerenciar o fluxo de login, logout e troca de senha no primeiro acesso.
 */

if (!defined('FINZY_BOOTSTRAP')) {
    http_response_code(403);
    exit('Acesso proibido.');
}

require_once __DIR__ . '/../helpers/AuthHelper.php';
require_once __DIR__ . '/../helpers/MailHelper.php';
require_once __DIR__ . '/../models/UsuarioModel.php';
require_once __DIR__ . '/../models/TokenRecuperacaoModel.php';

class AuthController {
    private UsuarioModel $usuarioModel;
    private TokenRecuperacaoModel $tokenModel;

    public function __construct() {
        $this->usuarioModel = new UsuarioModel();
        $this->tokenModel = new TokenRecuperacaoModel();
    }

    /**
     * Exibe o formulário de login.
     */
    public function exibirLogin(?string $erro = null, ?string $sucesso = null): void {
        AuthHelper::initSession();

        // Se já estiver logado e não for primeiro acesso, redireciona para a home/dashboard
        if (AuthHelper::isAuthenticated()) {
            if (AuthHelper::isFirstAccess()) {
                header('Location: ?route=primeiro_acesso');
                exit;
            }
            header('Location: ?route=dashboard');
            exit;
        }

        $csrfToken = AuthHelper::generateCsrfToken();
        require __DIR__ . '/../views/auth/login.php';
    }

    /**
     * Processa a tentativa de login via POST.
     */
    public function processarLogin(): void {
        AuthHelper::initSession();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?route=login');
            exit;
        }

        $tokenCsrf = $_POST['csrf_token'] ?? '';
        if (!AuthHelper::validateCsrfToken($tokenCsrf)) {
            $this->exibirLogin('Sessão ou formulário inválido. Por favor, tente novamente.');
            return;
        }

        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $senha = $_POST['senha'] ?? '';

        if (!$email || empty($senha)) {
            $this->exibirLogin('Por favor, preencha o e-mail e a senha.');
            return;
        }

        $usuario = $this->usuarioModel->buscarPorEmail($email);

        // Verificação segura de credenciais (mensagem genérica para não revelar existência do e-mail)
        if (!$usuario || !password_verify($senha, $usuario['senha_hash'])) {
            $this->exibirLogin('E-mail ou senha incorretos.');
            return;
        }

        // Verifica se a conta está ativa
        if ($usuario['status'] !== 'ativo') {
            $this->exibirLogin('Sua conta está inativa. Entre em contato com o Administrador.');
            return;
        }

        // Loga o usuário na sessão
        AuthHelper::loginUser($usuario);

        // Se for o primeiro acesso, direciona obrigatoriamente para a troca de senha
        if ((int)$usuario['primeiro_acesso'] === 1) {
            header('Location: ?route=primeiro_acesso');
            exit;
        }

        header('Location: ?route=dashboard');
        exit;
    }

    /**
     * Exibe a tela de solicitação de recuperação de senha.
     */
    public function exibirEsqueciSenha(?string $erro = null, ?string $sucesso = null): void {
        AuthHelper::initSession();

        if (AuthHelper::isAuthenticated()) {
            header('Location: ?route=dashboard');
            exit;
        }

        $csrfToken = AuthHelper::generateCsrfToken();
        require __DIR__ . '/../views/auth/esqueci_senha.php';
    }

    /**
     * Processa a solicitação de recuperação de senha via POST.
     */
    public function processarEsqueciSenha(): void {
        AuthHelper::initSession();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?route=esqueci_senha');
            exit;
        }

        $tokenCsrf = $_POST['csrf_token'] ?? '';
        if (!AuthHelper::validateCsrfToken($tokenCsrf)) {
            $this->exibirEsqueciSenha('Sessão ou formulário inválido. Por favor, tente novamente.');
            return;
        }

        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        
        // Mensagem genérica para segurança (evita enumeração de e-mails de usuários)
        $mensagemGenericaSucesso = 'Se o e-mail informado estiver cadastrado e ativo em nosso sistema, você receberá as instruções com o link de redefinição de senha.';

        if (!$email) {
            $this->exibirEsqueciSenha('Por favor, informe um endereço de e-mail válido.');
            return;
        }

        $usuario = $this->usuarioModel->buscarPorEmail($email);

        if ($usuario && $usuario['status'] === 'ativo') {
            $token = $this->tokenModel->gerarToken((int)$usuario['id']);
            MailHelper::enviarEmailRecuperacao($usuario['email'], $usuario['nome'], $token);
        }

        // Exibe sempre a mesma mensagem genérica por razões de segurança
        $this->exibirEsqueciSenha(null, $mensagemGenericaSucesso);
    }

    /**
     * Exibe a tela de redefinição de senha para usuários que clicaram no link temporário.
     */
    public function exibirRedefinirSenha(?string $erro = null): void {
        AuthHelper::initSession();

        $token = $_GET['token'] ?? '';

        if (empty($token)) {
            $this->exibirLogin('Token de redefinição de senha não informado.');
            return;
        }

        $tokenValido = $this->tokenModel->buscarTokenValido($token);

        if (!$tokenValido) {
            $this->exibirEsqueciSenha('O link de redefinição de senha é inválido, foi utilizado ou expirou. Por favor, solicite um novo link.');
            return;
        }

        $csrfToken = AuthHelper::generateCsrfToken();
        require __DIR__ . '/../views/auth/redefinir_senha.php';
    }

    /**
     * Processa a criação da nova senha via token temporário.
     */
    public function processarRedefinirSenha(): void {
        AuthHelper::initSession();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?route=login');
            exit;
        }

        $tokenCsrf = $_POST['csrf_token'] ?? '';
        if (!AuthHelper::validateCsrfToken($tokenCsrf)) {
            $this->exibirEsqueciSenha('Sessão ou formulário inválido. Por favor, tente novamente.');
            return;
        }

        $token = $_POST['token'] ?? '';
        $novaSenha = $_POST['nova_senha'] ?? '';
        $confirmacaoSenha = $_POST['confirmacao_senha'] ?? '';

        $tokenValido = $this->tokenModel->buscarTokenValido($token);

        if (!$tokenValido) {
            $this->exibirEsqueciSenha('O link de redefinição de senha é inválido, foi utilizado ou expirou. Por favor, solicite um novo link.');
            return;
        }

        if (empty($novaSenha) || empty($confirmacaoSenha)) {
            $this->exibirRedefinirSenha('Preencha a nova senha e a confirmação.');
            return;
        }

        if (mb_strlen($novaSenha, 'UTF-8') < 6) {
            $this->exibirRedefinirSenha('A nova senha deve ter no mínimo 6 caracteres.');
            return;
        }

        if ($novaSenha !== $confirmacaoSenha) {
            $this->exibirRedefinirSenha('A confirmação da senha não confere com a nova senha.');
            return;
        }

        $usuarioId = (int)$tokenValido['usuario_id'];

        // Atualiza a senha do usuário
        $sucessoAtualizacao = $this->usuarioModel->atualizarSenha($usuarioId, $novaSenha);

        if ($sucessoAtualizacao) {
            // Marca o token como utilizado
            $this->tokenModel->marcarComoUsado($token);
            $this->exibirLogin(null, 'Sua senha foi redefinida com sucesso! Você já pode entrar com a nova senha.');
        } else {
            $this->exibirRedefinirSenha('Ocorreu um erro ao atualizar sua senha. Tente novamente.');
        }
    }

    /**
     * Exibe a tela de troca obrigatória de senha no primeiro acesso.
     */
    public function exibirPrimeiroAcesso(?string $erro = null): void {
        AuthHelper::requireLogin();

        // Se não for o primeiro acesso, redireciona para a aplicação
        if (!AuthHelper::isFirstAccess()) {
            header('Location: ?route=dashboard');
            exit;
        }

        $usuarioLogado = AuthHelper::getLoggedUser();
        $csrfToken = AuthHelper::generateCsrfToken();
        require __DIR__ . '/../views/auth/primeiro_acesso.php';
    }

    /**
     * Processa a troca de senha do primeiro acesso.
     */
    public function processarPrimeiroAcesso(): void {
        AuthHelper::requireLogin();

        if (!AuthHelper::isFirstAccess()) {
            header('Location: ?route=dashboard');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?route=primeiro_acesso');
            exit;
        }

        $tokenCsrf = $_POST['csrf_token'] ?? '';
        if (!AuthHelper::validateCsrfToken($tokenCsrf)) {
            $this->exibirPrimeiroAcesso('Sessão ou formulário inválido. Por favor, tente novamente.');
            return;
        }

        $novaSenha = $_POST['nova_senha'] ?? '';
        $confirmacaoSenha = $_POST['confirmacao_senha'] ?? '';

        if (empty($novaSenha) || empty($confirmacaoSenha)) {
            $this->exibirPrimeiroAcesso('Preencha a nova senha e a confirmação.');
            return;
        }

        if (mb_strlen($novaSenha, 'UTF-8') < 6) {
            $this->exibirPrimeiroAcesso('A nova senha deve ter no mínimo 6 caracteres.');
            return;
        }

        if ($novaSenha !== $confirmacaoSenha) {
            $this->exibirPrimeiroAcesso('A confirmação da senha não confere com a nova senha.');
            return;
        }

        $usuarioLogado = AuthHelper::getLoggedUser();

        // Atualiza a senha no banco de dados e marca primeiro_acesso = 0
        $sucesso = $this->usuarioModel->atualizarSenha($usuarioLogado['id'], $novaSenha);

        if ($sucesso) {
            $_SESSION['primeiro_acesso'] = 0;
            header('Location: ?route=dashboard&senha_alterada=1');
            exit;
        } else {
            $this->exibirPrimeiroAcesso('Ocorreu um erro ao atualizar sua senha. Tente novamente.');
        }
    }

    /**
     * Realiza o logout do usuário.
     */
    public function logout(): void {
        AuthHelper::logout();
        header('Location: ?route=login&logged_out=1');
        exit;
    }
}
