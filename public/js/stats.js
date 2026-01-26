// stats.js
function initStatsModule(container) {
    const menuSelect = container.querySelector('#filter-menu');
    const addMenuBtn = container.querySelector('#add-menu-btn');
    const menuTagsContainer = container.querySelector('#menu-tags');
    const chartCanvas = container.querySelector('#statsChart');
    const startDateInput = container.querySelector('#filter-start');
    const endDateInput = container.querySelector('#filter-end');

    if (!chartCanvas) return;

    let statsChartInstance;
    let selectedMenus = [];

    // =====================
    // Mettre à jour le chart
    // =====================
    async function loadStatsChart() {
        try {
            const start = startDateInput?.value;
            const end = endDateInput?.value;

            const params = new URLSearchParams();
            if (start) params.append('start', start);
            if (end) params.append('end', end);
            selectedMenus.forEach(m => params.append('menu[]', m));

            const resp = await fetch(`/admin/stats-data?${params.toString()}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            const data = await resp.json();

            console.log('Données reçues pour Chart.js:', data); // 🔹 utile pour debug

            if (!data.labels || !data.counts || !data.ca) {
                console.warn('JSON incomplet pour le chart.');
                return;
            }

            if (statsChartInstance) statsChartInstance.destroy();

            statsChartInstance = new Chart(chartCanvas.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [
                        { label: 'Nombre de commandes', data: data.counts, backgroundColor: '#A28873' },
                        { label: 'Chiffre d’affaires (€)', data: data.ca, backgroundColor: '#5A3E36' }
                    ]
                },
                options: {
                    responsive: true,
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    if (context.dataset.label === 'Chiffre d’affaires (€)') {
                                        return context.dataset.label + ': ' + context.raw.toLocaleString('fr-FR', { style: 'currency', currency: 'EUR' });
                                    }
                                    return context.dataset.label + ': ' + context.raw;
                                }
                            }
                        }
                    },
                    scales: { y: { beginAtZero: true } }
                }
            });
        } catch (err) {
            console.error('Erreur lors du chargement du chart:', err);
        }
    }

    // =====================
    // Créer un tag menu
    // =====================
    function createTag(menuId, menuName) {
        const tag = document.createElement('span');
        tag.classList.add('menu-tag');
        tag.textContent = menuName;

        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.textContent = 'x';
        removeBtn.addEventListener('click', () => {
            selectedMenus = selectedMenus.filter(m => m !== menuId);
            menuTagsContainer.removeChild(tag);
            loadStatsChart();
        });

        tag.appendChild(removeBtn);
        menuTagsContainer.appendChild(tag);
    }

    // =====================
    // Ajouter un menu depuis le select
    // =====================
    addMenuBtn?.addEventListener('click', () => {
        const selectedOption = menuSelect.options[menuSelect.selectedIndex];
        const menuId = selectedOption.value;
        const menuName = selectedOption.dataset.name || selectedOption.text;

        if (!menuId || selectedMenus.includes(menuId)) return;

        selectedMenus.push(menuId);
        createTag(menuId, menuName);
        loadStatsChart();
    });

    // =====================
    // Rechargement automatique si dates changent
    // =====================
    [startDateInput, endDateInput].forEach(input => {
        if (input) input.addEventListener('change', loadStatsChart);
    });

    // =====================
    // Chargement initial
    // =====================
    loadStatsChart();
}

// =====================
// Chargement si la page charge la section stats directement
// =====================
document.addEventListener('DOMContentLoaded', () => {
    const container = document.querySelector('.stats-list');
    if (container) initStatsModule(container);
});

// =====================
// Chargement si la section stats est insérée via AJAX
// =====================
document.addEventListener('statsContentLoaded', (e) => {
    const container = e.detail.container;
    if (container) initStatsModule(container);
});