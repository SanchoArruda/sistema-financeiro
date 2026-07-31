<?php
/**
 * Finzy — Helper de Autenticação, Sessão e CSRF (AuthHelper)
 * 
 * Gerencia verificação de login, tempo de inatividade, permissões RBAC e tokens CSRF.
 */

if (!defined('FINZY_BOOTSTRAP')) {
    http_response_code(403);
    exit('Acesso proibido.');
}

class AuthHelper {

    /**
     * Inicia a sessão PHP com parâmetros de segurança se ainda não tiver sido iniciada.
     */
    public static function initSession(): void {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_httponly', '1');
            ini_set('session.cookie_samesite', 'Lax');
            session_start();
        }
    }

    /**
     * Registra o usuário na sessão no login bem-sucedido.
     * 
     * @param array $usuario Dados do usuário retornados pelo model
     */
    public static function loginUser(array $usuario): void {
        self::initSession();
        
        // Regenera a ID de sessão por segurança contra Session Fixation
        session_regenerate_id(true);

        $_SESSION['usuario_id']     = (int) $usuario['id'];
        $_SESSION['usuario_nome']   = (string) $usuario['nome'];
        $_SESSION['usuario_email']  = (string) $usuario['email'];
        $_SESSION['usuario_perfil'] = (string) $usuario['perfil'];
        $_SESSION['primeiro_acesso'] = (int) $usuario['primeiro_acesso'];
        $_SESSION['ultimo_acesso']  = time();
    }

    /**
     * Encerra a sessão do usuário de forma segura.
     */
    public static function logout(): void {
        self::initSession();
        
        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        session_destroy();
    }

    /**
     * Verifica se o usuário está autenticado e se a sessão não expirou por inatividade.
     * 
     * @return bool
     */
    public static function isAuthenticated(): bool {
        self::initSession();

        if (empty($_SESSION['usuario_id'])) {
            return false;
        }

        // Tempo limite de inatividade (em segundos) — Padrão: 30 minutos (1800s)
        $timeoutMinutos = defined('DEFAULT_SESSION_TIMEOUT') ? DEFAULT_SESSION_TIMEOUT : 30;
        $timeoutSegundos = $timeoutMinutos * 60;

        if (isset($_SESSION['ultimo_acesso'])) {
            $tempoInativo = time() - $_SESSION['ultimo_acesso'];
            if ($tempoInativo > $timeoutSegundos) {
                self::logout();
                return false;
            }
        }

        // Atualiza timestamp do último acesso
        $_SESSION['ultimo_acesso'] = time();
        return true;
    }

    /**
     * Retorna os dados do usuário atualmente conectado.
     * 
     * @return array|null
     */
    public static function getLoggedUser(): ?array {
        if (!self::isAuthenticated()) {
            return null;
        }

        return [
            'id'              => $_SESSION['usuario_id'],
            'nome'            => $_SESSION['usuario_nome'],
            'email'           => $_SESSION['usuario_email'],
            'perfil'          => $_SESSION['usuario_perfil'],
            'primeiro_acesso' => $_SESSION['primeiro_acesso']
        ];
    }

    /**
     * Verifica se é o primeiro acesso do usuário (exige troca de senha).
     * 
     * @return bool
     */
    public static function isFirstAccess(): bool {
        self::initSession();
        return !empty($_SESSION['primeiro_acesso']) && $_SESSION['primeiro_acesso'] === 1;
    }

    /**
     * Exige que o usuário esteja autenticado. Caso contrário, redireciona para a página de login.
     * 
     * @param string $redirectUrl URL para redirecionar se não autenticado
     */
    public static function requireLogin(string $redirectUrl = '?route=login'): void {
        if (!self::isAuthenticated()) {
            header("Location: {$redirectUrl}");
            exit;
        }
    }

    /**
     * Exige que o usuário conclua a troca de senha do primeiro acesso.
     */
    public static function checkFirstAccessRedirect(): void {
        if (self::isAuthenticated() && self::isFirstAccess()) {
            $route = $_GET['route'] ?? '';
            if ($route !== 'primeiro_acesso' && $route !== 'processar_primeiro_acesso' && $route !== 'logout') {
                header("Location: ?route=primeiro_acesso");
                exit;
            }
        }
    }

    /**
     * Gera ou recupera o token CSRF da sessão ativa.
     * 
     * @return string
     */
    public static function generateCsrfToken(): string {
        self::initSession();

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }

    /**
     * Valida o token CSRF enviado via POST contra o token da sessão.
     * 
     * @param string|null $token Token recebido no formulário
     * @return bool
     */
    public static function validateCsrfToken(?string $token): bool {
        self::initSession();

        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }

        return hash_equals($_SESSION['csrf_token'], $token);
    }
}
