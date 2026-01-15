document.addEventListener('DOMContentLoaded', function() {

    const dashboardContent = document.querySelector('.dashboard-content');

    // -------------------------------
    // Chargement AJAX
    // -------------------------------
    function loadAjaxContent(url) {
        if (!url) return;
        fetch(url)
            .then(resp => resp.text())
            .then(html => {
                dashboardContent.innerHTML = html;
                attachCommandeFilters(); // On rattache le filtre après chaque chargement
                attachAnnulationFields(); // On rattache la logique des champs annulation
            })
            .catch(err => console.error('Erreur AJAX:', err));
    }

    // -------------------------------
    // Boutons AJAX
    // -------------------------------
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.ajax-btn');
        if (!btn) return;

        e.preventDefault();
        const url = btn.dataset.url;
        if (!url) return;

        document.querySelectorAll('.ajax-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        loadAjaxContent(url);
    });

    // -------------------------------
    // Chargement par défaut
    // -------------------------------
    const defaultBtn = document.querySelector('.ajax-btn[data-default="true"]');
    if (defaultBtn) {
        defaultBtn.classList.add('active');
        loadAjaxContent(defaultBtn.dataset.url);
    }

    // -------------------------------
    // Filtre commandes
    // -------------------------------
    function attachCommandeFilters() {
        const filterToggle = document.querySelector('.filter-toggle');
        const filterPanel = document.querySelector('.filter-panel');
        const filterOverlay = document.querySelector('.filter-overlay');
        const closePanel = document.querySelector('.close-panel');
        const applyBtn = document.querySelector('.apply-filters');
        const resetBtn = document.querySelector('.reset-filters');
        const selectStatut = document.getElementById('statut-filter');

        const tableContainer = document.querySelector('.commande-table')?.parentNode;
        if (!filterToggle || !filterPanel || !filterOverlay || !tableContainer) return;

        const urlCommandeList = filterToggle.dataset.url;

        // ouvrir le panneau
        filterToggle.addEventListener('click', () => {
            filterPanel.classList.add('active');
            filterOverlay.classList.add('active');
        });

        // fermer le panneau
        function closeFilterPanel() {
            filterPanel.classList.remove('active');
            filterOverlay.classList.remove('active');
        }
        closePanel?.addEventListener('click', closeFilterPanel);
        filterOverlay?.addEventListener('click', closeFilterPanel);

        // charger liste filtrée
        function loadFilteredList(statutId = '') {
            const url = new URL(urlCommandeList, window.location.origin);
            if (statutId) url.searchParams.set('statutId', statutId);

            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(resp => resp.text())
                .then(html => {
                    tableContainer.innerHTML = html;
                    attachCommandeFilters();
                    attachAnnulationFields(); // remettre la logique sur le nouveau contenu
                })
                .catch(err => console.error('Erreur AJAX :', err));
        }

        // appliquer
        applyBtn?.addEventListener('click', () => {
            loadFilteredList(selectStatut?.value);
            closeFilterPanel();
        });

        // réinitialiser
        resetBtn?.addEventListener('click', () => {
            if (selectStatut) selectStatut.value = '';
            loadFilteredList();
            closeFilterPanel();
        });
    }

    // -------------------------------
    // Champs annulation dynamiques
    // -------------------------------
    function attachAnnulationFields() {
        const form = document.querySelector('form[name="commande"]');
        if (!form) return;

        const statutSelect = form.querySelector('select[name$="[statutCommande]"]');
        const motifField = form.querySelector('.form-group-motifAnnulation');
        const contactField = form.querySelector('.form-group-modeContact');

        function toggleAnnulationFields() {
            if (!statutSelect) return;
            const selected = statutSelect.selectedOptions[0]?.textContent.trim();
            if (selected === 'Annulé') {
                motifField?.classList.remove('hidden');
                contactField?.classList.remove('hidden');
            } else {
                motifField?.classList.add('hidden');
                contactField?.classList.add('hidden');
            }
        }

        // initial
        toggleAnnulationFields();

        // au changement
        statutSelect?.addEventListener('change', toggleAnnulationFields);
    }

});