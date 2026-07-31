/**
 * JavaScript Nativo — Finzy (ES6+)
 * 
 * Arquivo de suporte para interatividade do cliente.
 * Não utiliza frameworks JS complexos.
 */

document.addEventListener('DOMContentLoaded', () => {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const appSidebar = document.getElementById('appSidebar');
    const sidebarBackdrop = document.getElementById('sidebarBackdrop');

    // Restaura o estado salvo da sidebar retrátil no desktop
    if (appSidebar && window.innerWidth >= 992) {
        const isCollapsed = localStorage.getItem('finzy_sidebar_collapsed') === 'true';
        if (isCollapsed) {
            appSidebar.classList.add('collapsed');
        }
    }

    if (sidebarToggle && appSidebar) {
        sidebarToggle.addEventListener('click', () => {
            if (window.innerWidth >= 992) {
                // Desktop: comprime / expande a barra lateral
                appSidebar.classList.toggle('collapsed');
                const isCollapsed = appSidebar.classList.contains('collapsed');
                localStorage.setItem('finzy_sidebar_collapsed', isCollapsed ? 'true' : 'false');
            } else {
                // Mobile: exibe / oculta a barra lateral overlay
                appSidebar.classList.toggle('show');
                if (sidebarBackdrop) {
                    sidebarBackdrop.classList.toggle('show');
                }
            }
        });
    }

    if (sidebarBackdrop && appSidebar) {
        sidebarBackdrop.addEventListener('click', () => {
            appSidebar.classList.remove('show');
            sidebarBackdrop.classList.remove('show');
        });
    }
});
