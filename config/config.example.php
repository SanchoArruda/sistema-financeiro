<?php
/**
 * Arquivo de Exemplo de Configuração Principal — Finzy
 * 
 * Copie este arquivo para config/config.php e preencha com as credenciais reais do ambiente.
 * NUNCA acesse este arquivo diretamente pela URL. Ele é carregado apenas via require_once.
 */

// Impede acesso direto caso as regras de .htaccess falhem
if (!defined('FINZY_BOOTSTRAP')) {
    http_response_code(403);
    exit('Acesso proibido.');
}

// Configurações do Banco de Dados MySQL
define('DB_HOST', 'localhost');
define('DB_NAME', 'finzy');
define('DB_USER', 'usuario_banco');
define('DB_PASS', 'senha_banco');
define('DB_CHARSET', 'utf8mb4');

// Configurações de E-mail (SMTP) para recuperação de senha
define('SMTP_HOST', 'localhost');
define('SMTP_PORT', 587);
define('SMTP_USER', 'sistema@finzy.local');
define('SMTP_PASS', 'senha_smtp');
define('SMTP_FROM', 'noreply@finzy.local');
define('SMTP_FROM_NAME', 'Finzy - Gestão Financeira');

// Parâmetros do Sistema (Valores Padrão / Fallback)
define('LOG_ENABLED', true);
define('DEFAULT_SESSION_TIMEOUT', 30); // em minutos
define('DEFAULT_LOG_RETENTION_DAYS', 30); // em dias
define('APP_NAME', 'Finzy');
define('APP_URL', 'http://localhost/sistema_financeiro');
