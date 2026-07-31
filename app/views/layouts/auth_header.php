<?php
/**
 * Finzy — Layout Cabeçalho de Autenticação (auth_header.php)
 */

if (!defined('FINZY_BOOTSTRAP')) {
    http_response_code(403);
    exit('Acesso proibido.');
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($tituloPagina ?? 'Finzy — Gestão Financeira', ENT_QUOTES, 'UTF-8'); ?></title>
    <!-- Bootstrap 5 Local -->
    <link rel="stylesheet" href="assets/bootstrap/bootstrap.min.css">
    <!-- Design System Fiscal Precision -->
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body class="bg-surface text-on-surface d-flex flex-column min-vh-100 justify-content-center align-items-center py-4">
