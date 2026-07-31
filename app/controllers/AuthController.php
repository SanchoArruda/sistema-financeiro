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
require_once __DIR__ . '/../models/UsuarioModel.php';

class AuthController {
    private UsuarioModel $usuarioModel;

    public function __construct() {
        $this->usuarioModel = new UsuarioModel();
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
