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
        if (class_exists('ConfiguracaoModel')) {
            $valConfig = (int) ConfiguracaoModel::obterValor('tempo_sessao_minutos', (string) $timeoutMinutos);
            if ($valConfig >= 5 && $valConfig <= 480) {
                $timeoutMinutos = $valConfig;
            }
        }
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

    /**
     * Verifica se o usuário autenticado possui determinado perfil/papel.
     * 
     * @param string|array $perfis Permite um perfil em string ou array de perfis permitidos
     * @return bool
     */
    public static function hasRole(string|array $perfis): bool {
        if (!self::isAuthenticated()) {
            return false;
        }

        $perfilAtual = $_SESSION['usuario_perfil'] ?? '';
        $perfisPermitidos = is_array($perfis) ? $perfis : [$perfis];

        return in_array($perfilAtual, $perfisPermitidos, true);
    }

    /**
     * Verifica se o usuário autenticado é um Administrador.
     * 
     * @return bool
     */
    public static function isAdmin(): bool {
        return self::hasRole('administrador');
    }

    /**
     * Verifica se o usuário autenticado é um Operador.
     * 
     * @return bool
     */
    public static function isOperador(): bool {
        return self::hasRole('operador');
    }

    /**
     * Exige que o usuário possua um dos perfis autorizados.
     * Se não possuir, grava log de segurança e exibe a tela de Acesso Negado (403).
     * 
     * @param string|array $perfis Perfis permitidos para acessar a ação/rota
     */
    public static function requireRole(string|array $perfis): void {
        self::requireLogin();

        if (!self::hasRole($perfis)) {
            $rotaSolicitada = $_GET['route'] ?? 'desconhecida';
            $perfilAtual = $_SESSION['usuario_perfil'] ?? 'Sem Perfil';
            
            if (class_exists('LogHelper')) {
                LogHelper::logSecurity('acesso_negado', [
                    'rota' => $rotaSolicitada,
                    'perfil_usuario' => $perfilAtual,
                    'perfis_requeridos' => (array) $perfis
                ]);
            }

            self::exibirAcessoNegado();
        }
    }

    /**
     * Exige que o usuário conectado seja Administrador.
     */
    public static function requireAdmin(): void {
        self::requireRole('administrador');
    }

    /**
     * Define o código HTTP 403 e renderiza a View de Acesso Negado.
     */
    public static function exibirAcessoNegado(): void {
        http_response_code(403);
        $viewPath = __DIR__ . '/../views/auth/acesso_negado.php';
        
        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            echo "<h1>403 — Acesso Negado</h1><p>Você não tem permissão para acessar este recurso.</p><a href='?route=dashboard'>Voltar ao Início</a>";
        }
        exit;
    }
}
