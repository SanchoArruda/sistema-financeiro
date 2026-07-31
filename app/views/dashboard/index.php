<?php
/**
 * Finzy — Dashboard Inicial (app/views/dashboard/index.php)
 * 
 * Visão inicial acessível após autenticação bem-sucedida.
 */

if (!defined('FINZY_BOOTSTRAP')) {
    http_response_code(403);
    exit('Acesso proibido.');
}

$usuario = AuthHelper::getLoggedUser();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Principal — Finzy</title>
    <!-- Bootstrap 5 Local -->
    <link rel="stylesheet" href="assets/bootstrap/bootstrap.min.css">
    <!-- Design System Fiscal Precision -->
    <link rel="stylesheet" href="assets/css/app.css">
</head>
<body class="bg-surface text-on-surface">
    <!-- Barra Superior / Header Simplificado -->
    <header class="navbar navbar-expand-lg bg-primary navbar-dark px-4 shadow-sm" style="background-color: var(--color-primary) !important;">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold fs-4" href="?route=dashboard">
                <?php echo htmlspecialchars(APP_NAME, ENT_QUOTES, 'UTF-8'); ?>
            </a>
            <div class="d-flex align-items-center gap-3">
                <span class="text-white small">
                    Olá, <strong><?php echo htmlspecialchars($usuario['nome'], ENT_QUOTES, 'UTF-8'); ?></strong> 
                    <span class="badge bg-secondary ms-1"><?php echo htmlspecialchars(ucfirst($usuario['perfil']), ENT_QUOTES, 'UTF-8'); ?></span>
                </span>
                <a href="?route=logout" class="btn btn-outline-light btn-sm">Sair</a>
            </div>
        </div>
    </header>

    <main class="container py-5">
        <?php if (isset($_GET['senha_alterada']) && $_GET['senha_alterada'] === '1'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Senha alterada com sucesso!</strong> Seu primeiro acesso foi concluído com segurança.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card p-4 border rounded-3 bg-white shadow-sm mb-4">
            <h2 class="h4 text-primary mb-3">Bem-vindo ao Finzy!</h2>
            <p class="text-muted">
                Você está autenticado no sistema com perfil de <strong><?php echo htmlspecialchars(ucfirst($usuario['perfil']), ENT_QUOTES, 'UTF-8'); ?></strong>.
            </p>
            <div class="badge bg-secondary text-white p-2 mb-3 align-self-start">
                Fase 3 — Autenticação, Sessão e Troca de Senha Operacionais
            </div>
            <p class="small text-secondary mb-0">
                Sessão mantida com cookies HttpOnly/SameSite=Lax e tempo limite de inatividade configurado para 30 minutos.
            </p>
        </div>
    </main>

    <script src="assets/bootstrap/bootstrap.bundle.min.js"></script>
</body>
</html>
