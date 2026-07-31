<?php
/**
 * Finzy — Sistema de Gestão Financeira
 * 
 * Ponto de entrada único da aplicação (Front Controller).
 * Todas as requisições passam por este arquivo.
 */

// Define a constante global de inicialização do sistema
define('FINZY_BOOTSTRAP', true);

// Define fuso horário padrão do Brasil
date_default_timezone_set('America/Sao_Paulo');

// Carrega o arquivo de configuração principal
if (file_exists(__DIR__ . '/config/config.php')) {
    require_once __DIR__ . '/config/config.php';
} else {
    http_response_code(500);
    exit('Erro crítico: Arquivo de configuração (config/config.php) não foi encontrado.');
}

// Configuração de erros
ini_set('display_errors', '1');
error_reporting(E_ALL);

// Carrega os utilitários básicos e controllers
require_once __DIR__ . '/app/models/Database.php';
require_once __DIR__ . '/app/helpers/LogHelper.php';
require_once __DIR__ . '/app/helpers/AuthHelper.php';
require_once __DIR__ . '/app/controllers/AuthController.php';

// Configuração de manipuladores globais de erros e exceções
set_exception_handler(function (Throwable $e) {
    LogHelper::logError('Exceção não tratada', $e);
    
    if (!headers_sent()) {
        http_response_code(500);
    }

    $tituloPagina = 'Erro Inesperado — ' . (defined('APP_NAME') ? APP_NAME : 'Finzy');
    $headerPath = __DIR__ . '/app/views/layouts/auth_header.php';
    $footerPath = __DIR__ . '/app/views/layouts/auth_footer.php';

    if (file_exists($headerPath) && file_exists($footerPath)) {
        require $headerPath;
        echo '
        <main class="container max-w-md w-100" style="max-width: 480px;">
            <div class="card shadow-sm p-4 border rounded-3 bg-white text-center">
                <div class="mb-3">
                    <div class="d-inline-flex align-items-center justify-content-center bg-danger-subtle text-danger rounded-circle" style="width: 64px; height: 64px;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" class="bi bi-exclamation-triangle" viewBox="0 0 16 16">
                            <path d="M7.938 2.016A.13.13 0 0 1 8.002 2a.13.13 0 0 1 .063.016.15.15 0 0 1 .054.057l6.857 11.667c.036.06.035.124.002.183a.2.2 0 0 1-.054.06.1.1 0 0 1-.066.017H1.146a.1.1 0 0 1-.066-.017.2.2 0 0 1-.054-.06.18.18 0 0 1 .002-.183L7.884 2.073a.15.15 0 0 1 .054-.057zm1.044-.45a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767z"/>
                            <path d="M7.002 12a1 1 0 1 0 2 0 1 1 0 0 0-2 0M7.1 5.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/>
                        </svg>
                    </div>
                </div>
                <h1 class="h4 fw-bold text-dark mb-2">Ocorreu um erro inesperado</h1>
                <p class="text-muted mb-4" style="font-size: 0.95rem;">
                    Nossa equipe de sistema já registrou o incidente. Por favor, tente novamente em alguns instantes.
                </p>
                <div>
                    <a href="?route=dashboard" class="btn btn-primary px-4 py-2 fw-semibold" style="background-color: var(--color-primary); border-color: var(--color-primary);">
                        Voltar para o Início
                    </a>
                </div>
            </div>
        </main>';
        require $footerPath;
    } else {
        echo "<h1>Erro Inesperado</h1><p>Ocorreu um erro inesperado. Tente novamente mais tarde.</p>";
    }
    exit;
});

set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) {
        return false;
    }
    LogHelper::logError("Erro PHP [{$errno}]: {$errstr}", null, ['file' => $errfile, 'line' => $errline]);
    return true;
});

// Inicializa a sessão segura
AuthHelper::initSession();

// Rota solicitada via query string (padrão: dashboard ou login dependendo do estado)
$route = $_GET['route'] ?? 'dashboard';

// Lista de rotas públicas (acessíveis sem login)
$publicRoutes = [
    'login', 
    'processar_login', 
    'esqueci_senha', 
    'processar_esqueci_senha', 
    'redefinir_senha', 
    'processar_redefinir_senha'
];

// Se não estiver autenticado e tentar acessar rota protegida, redireciona para login
if (!AuthHelper::isAuthenticated() && !in_array($route, $publicRoutes, true)) {
    header('Location: ?route=login');
    exit;
}

// Se estiver autenticado e for primeiro acesso, força redirecionamento para troca de senha
AuthHelper::checkFirstAccessRedirect();

// Roteamento de Requisições
switch ($route) {
    case 'login':
        (new AuthController())->exibirLogin();
        break;

    case 'processar_login':
        (new AuthController())->processarLogin();
        break;

    case 'esqueci_senha':
        (new AuthController())->exibirEsqueciSenha();
        break;

    case 'processar_esqueci_senha':
        (new AuthController())->processarEsqueciSenha();
        break;

    case 'redefinir_senha':
        (new AuthController())->exibirRedefinirSenha();
        break;

    case 'processar_redefinir_senha':
        (new AuthController())->processarRedefinirSenha();
        break;

    case 'primeiro_acesso':
        (new AuthController())->exibirPrimeiroAcesso();
        break;

    case 'processar_primeiro_acesso':
        (new AuthController())->processarPrimeiroAcesso();
        break;

    case 'logout':
        (new AuthController())->logout();
        break;

    case 'dashboard':
        AuthHelper::requireLogin();
        require __DIR__ . '/app/views/dashboard/index.php';
        break;

    default:
        // Caso a rota não exista, direciona para o dashboard ou login
        if (AuthHelper::isAuthenticated()) {
            header('Location: ?route=dashboard');
        } else {
            header('Location: ?route=login');
        }
        exit;
}
