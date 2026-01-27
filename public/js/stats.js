// Chargement initial
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

    // Si les inputs de date sont vides, on met des dates par défaut
    if (!startDateInput.value) startDateInput.value = '2026-01-01';
    if (!endDateInput.value) endDateInput.value = new Date().toISOString().split('T')[0];

    async function loadStatsChart() {
        try {
            const start = startDateInput.value;
            const end = endDateInput.value;

            const params = new URLSearchParams();
            if (start) params.append('start', start);
            if (end) params.append('end', end);
            selectedMenus.forEach(m => params.append('menu[]', m));

            const resp = await fetch(`/admin/stats-data?${params.toString()}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            const data = await resp.json();
            if (!data.labels || !data.counts || !data.ca) return;

            if (statsChartInstance) {
                statsChartInstance.data.labels = data.labels;
                statsChartInstance.data.datasets[0].data = data.counts;
                statsChartInstance.data.datasets[1].data = data.ca;
                statsChartInstance.update();
            } else {
                statsChartInstance = new Chart(chartCanvas.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: data.labels,
                        datasets: [
                            { label: 'Nombre de commandes', data: data.counts, backgroundColor: '#A28873', yAxisID: 'y' },
                            { label: 'Chiffre d’affaires (€)', data: data.ca, backgroundColor: '#5A3E36', yAxisID: 'y1' }
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
                        scales: {
                            y: { type: 'linear', position: 'left', beginAtZero: true, title: { display: true, text: 'Nombre de commandes' } },
                            y1: { type: 'linear', position: 'right', beginAtZero: true, grid: { drawOnChartArea: false }, title: { display: true, text: 'Chiffre d’affaires (€)' } }
                        }
                    }
                });
            }
        } catch (err) {
            console.error('Erreur lors du chargement du chart:', err);
        }
    }

    // Événements
    [startDateInput, endDateInput].forEach(input => {
        if (input) input.addEventListener('change', loadStatsChart);
    });

    addMenuBtn?.addEventListener('click', () => {
        const selectedOption = menuSelect.options[menuSelect.selectedIndex];
        const menuId = selectedOption.value;
        const menuName = selectedOption.dataset.name || selectedOption.text;
        if (!menuId || selectedMenus.includes(menuId)) return;
        selectedMenus.push(menuId);
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
        loadStatsChart();
    });

    // Chargement initial avec les dates actuelles
    loadStatsChart();
}

document.addEventListener('DOMContentLoaded', () => {
    const container = document.querySelector('.stats-list');
    if (container) initStatsModule(container);
});

document.addEventListener('statsContentLoaded', (e) => {
    const container = e.detail.container;
    if (container) initStatsModule(container);
});