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

// Configuração inicial de exibição de erros (desativado display_errors em produção)
ini_set('display_errors', '1');
error_reporting(E_ALL);

// Inicializa a sessão PHP com cookies seguros
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Lax');
    session_start();
}

// Para a Fase 1 (Estrutura Inicial), apenas exibe status base da aplicação
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo defined('APP_NAME') ? APP_NAME : 'Finzy'; ?> — Banco de Dados e Migrations</title>
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body class="bg-surface text-on-surface">
    <main class="container mx-auto p-6 max-w-4xl text-center">
        <div class="card p-8 bg-surface-container rounded-lg border border-outline-variant shadow-sm mt-12">
            <h1 class="text-3xl font-bold text-primary mb-4"><?php echo defined('APP_NAME') ? APP_NAME : 'Finzy'; ?></h1>
            <p class="text-body-md text-on-surface-variant mb-6">
                A estrutura do banco de dados, conexão PDO e migrations (001 a 009) foram construídas com sucesso.
            </p>
            <div class="badge inline-block bg-secondary text-on-secondary px-4 py-2 rounded-md font-semibold text-sm mb-4">
                Fase 2 — Banco de Dados, Conexão e Migrations Concluídos
            </div>
            <p class="text-body-sm text-outline">
                Próxima etapa: Autenticação, Sessão e Troca de Senha (Fase 3).
            </p>
        </div>
    </main>
    <script src="assets/js/app.js"></script>
</body>
</html>
