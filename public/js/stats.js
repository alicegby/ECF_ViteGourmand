document.addEventListener('DOMContentLoaded', () => {
    const container = document.querySelector('.stats-list');
    if (!container) return;

    const startInput = container.querySelector('#filter-start');
    const endInput = container.querySelector('#filter-end');
    const menuSelect = container.querySelector('#filter-menu');
    const addMenuBtn = container.querySelector('#add-menu-btn');
    const menuTags = container.querySelector('#menu-tags');
    const canvas = container.querySelector('#statsChart');

    let chart = null;
    let selectedMenus = [];

    // Dates par défaut
    startInput.value ||= '2026-01-01';
    endInput.value ||= new Date().toISOString().split('T')[0];

    async function loadStats() {
        try {
            const params = new URLSearchParams({
                start: startInput.value,
                end: endInput.value
            });

            selectedMenus.forEach(name => params.append('menu[]', name));

            const res = await fetch('/admin/stats-data?' + params.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });

            const data = await res.json();

            const labels = data.labels.length ? data.labels : ['Aucun menu'];
            const counts = data.counts.length ? data.counts : [0];
            const ca = data.ca.length ? data.ca : [0];

            if (!chart) {
                chart = new Chart(canvas, {
                    type: 'bar',
                    data: {
                        labels,
                        datasets: [
                            { label: 'Nombre de commandes', data: counts, backgroundColor: '#A28873', yAxisID: 'y' },
                            { label: 'Chiffre d’affaires (€)', data: ca, backgroundColor: '#5A3E36', yAxisID: 'y1' }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label(context) {
                                        if (context.dataset.yAxisID === 'y1') {
                                            return context.dataset.label + ' : ' + context.raw.toLocaleString('fr-FR', { style: 'currency', currency: 'EUR' });
                                        }
                                        return context.dataset.label + ' : ' + context.raw;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: { display: true, text: 'Nombre de commandes' }
                            },
                            y1: {
                                beginAtZero: true,
                                position: 'right',
                                grid: { drawOnChartArea: false },
                                title: { display: true, text: 'CA (€)' }
                            }
                        }
                    }
                });
            } else {
                chart.data.labels = labels;
                chart.data.datasets[0].data = counts;
                chart.data.datasets[1].data = ca;
                chart.update();
            }
        } catch (e) {
            console.error('Erreur stats chart', e);
        }
    }

    // Filtres dates
    startInput.addEventListener('change', loadStats);
    endInput.addEventListener('change', loadStats);

    // Ajouter un menu
    addMenuBtn.addEventListener('click', () => {
        const option = menuSelect.selectedOptions[0];
        if (!option || selectedMenus.includes(option.value)) return;

        selectedMenus.push(option.value);

        const tag = document.createElement('span');
        tag.className = 'menu-tag';
        tag.textContent = option.value;

        const remove = document.createElement('button');
        remove.textContent = '×';
        remove.onclick = () => {
            selectedMenus = selectedMenus.filter(name => name !== option.value);
            tag.remove();
            loadStats();
        };

        tag.appendChild(remove);
        menuTags.appendChild(tag);

        loadStats();
    });

    // Chargement initial
    loadStats();

    // Auto refresh toutes les 5 secondes
    setInterval(loadStats, 5000); // toutes les 5 secondes
});