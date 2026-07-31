<?php
/**
 * Finzy — View de Acesso Negado (app/views/auth/acesso_negado.php)
 * 
 * Exibida quando um usuário tenta acessar uma funcionalidade para a qual não tem perfil ou permissão.
 */

if (!defined('FINZY_BOOTSTRAP')) {
    http_response_code(403);
    exit('Acesso proibido.');
}

$tituloPagina = 'Acesso Negado — ' . (defined('APP_NAME') ? APP_NAME : 'Finzy');
require __DIR__ . '/../layouts/auth_header.php';
?>

<main class="container max-w-md w-100" style="max-width: 480px;">
    <div class="card shadow-sm p-4 border rounded-3 bg-white text-center">
        <!-- Ícone de Alerta / Bloqueio -->
        <div class="mb-3">
            <div class="d-inline-flex align-items-center justify-content-center bg-danger-subtle text-danger rounded-circle" style="width: 72px; height: 72px;">
                <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" class="bi bi-shield-lock" viewBox="0 0 16 16">
                    <path d="M5.338 1.59a61 61 0 0 0-2.837.856.48.48 0 0 0-.328.39c-.554 4.157.726 7.19 2.253 9.188a10.7 10.7 0 0 0 2.287 2.233c.346.244.652.42.893.533q.18.085.293.118a1 1 0 0 0 .101.025 1 1 0 0 0 .1-.025q.114-.034.294-.118c.24-.113.546-.29.893-.533a10.7 10.7 0 0 0 2.287-2.233c1.527-1.997 2.807-5.031 2.253-9.188a.48.48 0 0 0-.328-.39c-.651-.213-1.75-.56-2.837-.855C9.552 1.29 8.531 1.067 8 1.067c-.53 0-1.552.223-2.662.524zM5.072.56C6.157.265 7.31 0 8 0s1.843.265 2.928.56c1.11.3 2.229.655 2.887.87a1.5 1.5 0 0 1 1.044 1.262c.599 4.497-.71 7.776-2.463 10.07a12 12 0 0 1-2.56 2.5 2 2 0 0 1-.94.385 2 2 0 0 1-.94-.385 12 12 0 0 1-2.56-2.5C1.638 10.468.328 7.189.928 2.692A1.5 1.5 0 0 1 1.972 1.43c.658-.215 1.777-.57 2.887-.87z"/>
                    <path d="M9.5 6.5a1.5 1.5 0 0 1-1 1.415l.385 1.99a.5.5 0 0 1-.491.595h-.788a.5.5 0 0 1-.49-.595l.384-1.99A1.5 1.5 0 1 1 9.5 6.5"/>
                </svg>
            </div>
        </div>

        <h1 class="h4 fw-bold text-dark mb-2">403 — Acesso Negado</h1>
        <p class="text-muted mb-4" style="font-size: 0.95rem;">
            Você não possui permissão para acessar este recurso ou realizar esta operação. 
            Se você acredita que isso é um erro, entre em contato com o Administrador do sistema.
        </p>

        <!-- Botão Retornar -->
        <div>
            <a href="?route=dashboard" class="btn btn-primary px-4 py-2 fw-semibold" style="background-color: var(--color-primary); border-color: var(--color-primary);">
                Voltar para o Início
            </a>
        </div>
    </div>
</main>

<?php require __DIR__ . '/../layouts/auth_footer.php'; ?>
