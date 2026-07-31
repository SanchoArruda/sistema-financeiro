<?php
/**
 * Finzy — Dashboard Inicial (app/views/dashboard/index.php)
 * 
 * Visão inicial acessível após autenticação bem-sucedida.
 * Segue o Design System Fiscal Precision.
 */

if (!defined('FINZY_BOOTSTRAP')) {
    http_response_code(403);
    exit('Acesso proibido.');
}

$usuario = AuthHelper::getLoggedUser();
$tituloPagina = 'Painel Principal — Finzy';
require __DIR__ . '/../layouts/header.php';
?>

<main class="container py-4 flex-grow-1">
    <?php if (isset($_GET['senha_alterada']) && $_GET['senha_alterada'] === '1'): ?>
        <div class="alert alert-success alert-dismissible fade show mb-4 shadow-sm" role="alert">
            <strong>Senha alterada com sucesso!</strong> Seu primeiro acesso foi concluído com segurança.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    <?php endif; ?>

    <div class="card p-4 border rounded-3 bg-white shadow-sm mb-4">
        <h2 class="h4 text-primary mb-3" style="color: var(--color-primary) !important;">Bem-vindo ao Finzy!</h2>
        <p class="text-muted">
            Você está autenticado no sistema como <strong><?php echo htmlspecialchars($usuario['nome'], ENT_QUOTES, 'UTF-8'); ?></strong> 
            com perfil de <span class="badge bg-secondary opacity-75 text-uppercase"><?php echo htmlspecialchars($usuario['perfil'], ENT_QUOTES, 'UTF-8'); ?></span>.
        </p>
        <div class="d-flex align-items-center gap-2 mb-3">
            <span class="badge bg-success text-white p-2">
                Fase 6 — Cadastros Básicos (Categorias, Formas de Pagamento e Contas) Concluída
            </span>
        </div>
        
        <?php if (AuthHelper::isAdmin()): ?>
            <div class="row g-3 mt-2">
                <div class="col-12 col-md-4">
                    <div class="card border rounded-3 h-100 p-3 bg-light">
                        <h5 class="h6 fw-bold text-dark mb-2">Categorias</h5>
                        <p class="small text-muted mb-3">Gerencie categorias de receitas e despesas.</p>
                        <a href="?route=categorias" class="btn btn-sm btn-outline-primary fw-semibold mt-auto">Acessar Categorias</a>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card border rounded-3 h-100 p-3 bg-light">
                        <h5 class="h6 fw-bold text-dark mb-2">Formas de Pagamento</h5>
                        <p class="small text-muted mb-3">Gerencie os meios de transação (PIX, Cartão, Dinheiro).</p>
                        <a href="?route=formas_pagamento" class="btn btn-sm btn-outline-primary fw-semibold mt-auto">Acessar Formas de Pagamento</a>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="card border rounded-3 h-100 p-3 bg-light">
                        <h5 class="h6 fw-bold text-dark mb-2">Contas Financeiras</h5>
                        <p class="small text-muted mb-3">Gerencie contas bancárias e acompanhe saldos.</p>
                        <a href="?route=contas" class="btn btn-sm btn-outline-primary fw-semibold mt-auto">Acessar Contas</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
