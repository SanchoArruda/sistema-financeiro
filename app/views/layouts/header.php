<?php
/**
 * Finzy — Layout Cabeçalho da Aplicação (header.php)
 * 
 * Layout com Dark Navy Sidebar conforme o Design System Fiscal Precision.
 */

if (!defined('FINZY_BOOTSTRAP')) {
    http_response_code(403);
    exit('Acesso proibido.');
}

$usuarioLogado = AuthHelper::getLoggedUser();
$routeAtual = $_GET['route'] ?? 'dashboard';

// Função auxiliar para marcar item ativo na sidebar
$isActive = function(array $routes) use ($routeAtual): string {
    return in_array($routeAtual, $routes, true) ? 'active' : '';
};
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
<body class="bg-surface text-on-surface">

<?php if ($usuarioLogado): ?>
    <div class="app-wrapper">
        <!-- Backdrop para Mobile -->
        <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

        <!-- Sidebar Lateral Escura (Dark Navy Sidebar) -->
        <aside class="app-sidebar" id="appSidebar">
            <!-- Brand Logo -->
            <a href="?route=dashboard" class="sidebar-brand">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-wallet2 text-info me-2" viewBox="0 0 16 16">
                    <path d="M12.136.326A1.5 1.5 0 0 1 14 1.78V3h.5A1.5 1.5 0 0 1 16 4.5v9a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 13.5v-9a1.5 1.5 0 0 1 1.432-1.499L12.136.326zM5.562 3H13V1.78a.5.5 0 0 0-.621-.484zM1.5 4a.5.5 0 0 0-.5.5v9a.5.5 0 0 0 .5.5h13a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5z"/>
                </svg>
                <span><?php echo htmlspecialchars(APP_NAME, ENT_QUOTES, 'UTF-8'); ?></span>
            </a>

            <!-- Navegação Principal -->
            <nav class="sidebar-nav">
                <div class="nav-section-title">Menu Principal</div>

                <a href="?route=dashboard" class="nav-link <?php echo $isActive(['dashboard']); ?>">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg>
                    <span>Dashboard</span>
                </a>

                <a href="?route=lancamentos" class="nav-link <?php echo $isActive(['lancamentos', 'lancamentos_novo', 'lancamentos_editar']); ?>">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/></svg>
                    <span>Lançamentos</span>
                </a>

                <a href="?route=relatorios" class="nav-link <?php echo $isActive(['relatorios', 'relatorios_exportar_csv', 'relatorios_exportar_pdf']); ?>">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 20V10M12 20V4M6 20v-6"/></svg>
                    <span>Relatórios</span>
                </a>

                <a href="?route=lixeira" class="nav-link <?php echo $isActive(['lixeira']); ?>">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 6h18M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/></svg>
                    <span>Lixeira</span>
                </a>

                <?php if (AuthHelper::isAdmin()): ?>
                    <div class="nav-section-title">Cadastros Básicos</div>

                    <a href="?route=categorias" class="nav-link <?php echo $isActive(['categorias', 'categorias_novo', 'categorias_editar']); ?>">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h7"/></svg>
                        <span>Categorias</span>
                    </a>

                    <a href="?route=formas_pagamento" class="nav-link <?php echo $isActive(['formas_pagamento', 'formas_pagamento_novo', 'formas_pagamento_editar']); ?>">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/></svg>
                        <span>Formas de Pagamento</span>
                    </a>

                    <a href="?route=contas" class="nav-link <?php echo $isActive(['contas', 'contas_novo', 'contas_editar']); ?>">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 21h18M3 10h18M5 6l7-3 7 3M4 10v11M20 10v11M8 14v3M12 14v3M16 14v3"/></svg>
                        <span>Contas Financeiras</span>
                    </a>

                    <div class="nav-section-title">Administração</div>

                    <a href="?route=usuarios" class="nav-link <?php echo $isActive(['usuarios', 'usuarios_novo', 'usuarios_editar']); ?>">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
                        <span>Gestão de Usuários</span>
                    </a>

                    <a href="?route=configuracoes" class="nav-link <?php echo $isActive(['configuracoes']); ?>">
                        <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><circle cx="12" cy="12" r="3"/></svg>
                        <span>Configurações</span>
                    </a>
                <?php endif; ?>
            </nav>
        </aside>

        <!-- Conteúdo Principal e Topo -->
        <div class="app-main">
            <!-- Header Superior (Top Bar) -->
            <header class="app-header">
                <div class="d-flex align-items-center gap-3">
                    <button class="btn-toggle-sidebar" id="sidebarToggle" title="Alternar Barra Lateral">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <span class="fw-semibold fs-5 text-dark">
                        <?php echo htmlspecialchars($tituloPagina ?? 'Painel Principal', ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                </div>

                <!-- Perfil do Usuário no Canto Direito -->
                <div class="d-flex align-items-center gap-3">
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center gap-2 text-decoration-none dropdown-toggle text-dark" id="userMenuDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-avatar-circle">
                                <?php 
                                    $nome = $usuarioLogado['nome'] ?? 'U';
                                    $partes = explode(' ', trim($nome));
                                    $iniciais = strtoupper(substr($partes[0], 0, 1) . (isset($partes[1]) ? substr($partes[1], 0, 1) : ''));
                                    echo htmlspecialchars($iniciais, ENT_QUOTES, 'UTF-8');
                                ?>
                            </div>
                            <div class="d-none d-md-block text-start" style="line-height: 1.2;">
                                <div class="fw-semibold small"><?php echo htmlspecialchars($usuarioLogado['nome'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
                                <span class="badge bg-secondary opacity-75 text-uppercase" style="font-size: 0.6rem;">
                                    <?php echo htmlspecialchars($usuarioLogado['perfil'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                                </span>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="userMenuDropdown">
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2" href="?route=meu_perfil">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                    Meu Perfil
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item d-flex align-items-center gap-2 text-danger" href="?route=logout">
                                    <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    Sair do Sistema
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>

            <!-- Área de Conteúdo da Página -->
            <main class="app-content p-4 flex-grow-1">
<?php else: ?>
    <!-- Container para Telas de Login / Autenticação -->
    <main class="d-flex align-items-center justify-content-center min-vh-100 p-3">
<?php endif; ?>
