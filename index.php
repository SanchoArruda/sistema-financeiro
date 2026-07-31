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
require_once __DIR__ . '/app/helpers/AuthHelper.php';
require_once __DIR__ . '/app/controllers/AuthController.php';

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
