/**
 * Finzy — JavaScript Específico do Dashboard (assets/js/dashboard.js)
 * 
 * Controla os atalhos de período e a renderização do gráfico de colunas.
 */

document.addEventListener('DOMContentLoaded', () => {
    // Seleção de elementos do filtro de período
    const periodoSelect = document.getElementById('periodoSelect');
    const customDateContainer = document.getElementById('customDateContainer');

    if (periodoSelect && customDateContainer) {
        periodoSelect.addEventListener('change', (e) => {
            if (e.target.value === 'personalizado') {
                customDateContainer.classList.remove('d-none');
            } else {
                customDateContainer.classList.add('d-none');
                document.getElementById('formFiltroPeriodo').submit();
            }
        });
    }
});
