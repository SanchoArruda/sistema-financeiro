<?php
/**
 * Finzy — Layout Cabeçalho da Aplicação (header.php)
 * 
 * Barra de navegação principal e topo do sistema com o Design System Fiscal Precision.
 */

if (!defined('FINZY_BOOTSTRAP')) {
    http_response_code(403);
    exit('Acesso proibido.');
}

$usuarioLogado = AuthHelper::getLoggedUser();
$routeAtual = $_GET['route'] ?? 'dashboard';
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
<body class="bg-surface text-on-surface d-flex flex-column min-vh-100">
    <!-- Barra Superior / Header Principal -->
    <header class="navbar navbar-expand-lg bg-primary navbar-dark shadow-sm py-2 px-4" style="background-color: var(--color-primary) !important;">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold fs-4 d-flex align-items-center gap-2" href="?route=dashboard">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-wallet2" viewBox="0 0 16 16">
                    <path d="M12.136.326A1.5 1.5 0 0 1 14 1.78V3h.5A1.5 1.5 0 0 1 16 4.5v9a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 13.5v-9a1.5 1.5 0 0 1 1.432-1.499L12.136.326zM5.562 3H13V1.78a.5.5 0 0 0-.621-.484zM1.5 4a.5.5 0 0 0-.5.5v9a.5.5 0 0 0 .5.5h13a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5z"/>
                </svg>
                <?php echo htmlspecialchars(APP_NAME, ENT_QUOTES, 'UTF-8'); ?>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain" aria-controls="navMain" aria-expanded="false" aria-label="Alternar navegação">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navMain">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link <?php echo $routeAtual === 'dashboard' ? 'active fw-bold' : ''; ?>" href="?route=dashboard">
                            Painel Principal
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo in_array($routeAtual, ['lancamentos', 'lancamentos_novo', 'lancamentos_editar'], true) ? 'active fw-bold' : ''; ?>" href="?route=lancamentos">
                            Lançamentos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $routeAtual === 'lixeira' ? 'active fw-bold' : ''; ?>" href="?route=lixeira">
                            Lixeira
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo in_array($routeAtual, ['relatorios', 'relatorios_exportar_csv', 'relatorios_exportar_pdf'], true) ? 'active fw-bold' : ''; ?>" href="?route=relatorios">
                            Relatórios
                        </a>
                    </li>
                    <?php if (AuthHelper::isAdmin()): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle <?php echo in_array($routeAtual, ['categorias', 'formas_pagamento', 'contas'], true) ? 'active fw-bold' : ''; ?>" href="#" id="dropdownCadastros" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Cadastros Básicos
                            </a>
                            <ul class="dropdown-menu shadow" aria-labelledby="dropdownCadastros">
                                <li><a class="dropdown-item <?php echo $routeAtual === 'categorias' ? 'active' : ''; ?>" href="?route=categorias">Categorias</a></li>
                                <li><a class="dropdown-item <?php echo $routeAtual === 'formas_pagamento' ? 'active' : ''; ?>" href="?route=formas_pagamento">Formas de Pagamento</a></li>
                                <li><a class="dropdown-item <?php echo $routeAtual === 'contas' ? 'active' : ''; ?>" href="?route=contas">Contas Financeiras</a></li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $routeAtual === 'usuarios' ? 'active fw-bold' : ''; ?>" href="?route=usuarios">
                                Gestão de Usuários
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $routeAtual === 'configuracoes' ? 'active fw-bold' : ''; ?>" href="?route=configuracoes">
                                Configurações
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>

                <div class="d-flex align-items-center gap-3">
                    <div class="text-white text-end d-none d-sm-block">
                        <a href="?route=meu_perfil" class="text-white text-decoration-none" title="Ver Meu Perfil">
                            <div class="fw-semibold small"><?php echo htmlspecialchars($usuarioLogado['nome'] ?? '', ENT_QUOTES, 'UTF-8'); ?></div>
                            <span class="badge bg-secondary opacity-75 text-uppercase" style="font-size: 0.65rem;">
                                <?php echo htmlspecialchars($usuarioLogado['perfil'] ?? '', ENT_QUOTES, 'UTF-8'); ?>
                            </span>
                        </a>
                    </div>
                    <a href="?route=meu_perfil" class="btn btn-outline-light btn-sm px-3 d-flex align-items-center gap-1 <?php echo $routeAtual === 'meu_perfil' ? 'active fw-bold' : ''; ?>" title="Meu Perfil">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                            <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                            <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
                        </svg>
                        Meu Perfil
                    </a>
                    <a href="?route=logout" class="btn btn-outline-light btn-sm px-3">Sair</a>
                </div>
            </div>
        </div>
    </header>
