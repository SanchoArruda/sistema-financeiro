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

    if (sidebarToggle && appSidebar) {
        sidebarToggle.addEventListener('click', () => {
            appSidebar.classList.toggle('show');
            if (sidebarBackdrop) {
                sidebarBackdrop.classList.toggle('show');
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
