document.addEventListener('DOMContentLoaded', function() {

    // Gestion des thèmes / sous-menus
    document.querySelectorAll('.theme-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const targetId = btn.dataset.target;
            if (!targetId) return;
            const submenu = document.getElementById(targetId);
            if (!submenu) return;
            submenu.classList.toggle('active');
            const arrow = btn.querySelector('.arrow');
            if (arrow) arrow.classList.toggle('rotated');
        });
    });

    const dashboardContent = document.querySelector('.dashboard-content');

    // -------------------------------
    // Chargement AJAX global
    // -------------------------------
    function loadAjaxContent(url) {
        if (!url) return;
        fetch(url)
            .then(resp => resp.text())
            .then(html => {
                dashboardContent.innerHTML = html;
                attachFilters();        // rattache tous les filtres après chargement
                attachAnnulationFields(); 
            })
            .catch(err => console.error('Erreur AJAX:', err));
    }

    // Boutons AJAX
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

    // Chargement par défaut
    const defaultBtn = document.querySelector('.ajax-btn[data-default="true"]');
    if (defaultBtn) {
        defaultBtn.classList.add('active');
        loadAjaxContent(defaultBtn.dataset.url);
    } 

    // -------------------------------
    // Filtre panel générique
    // -------------------------------
    function attachFilters() {
        document.querySelectorAll('.filter-toggle').forEach(toggleBtn => {
            const type = toggleBtn.dataset.type; // "commande" ou "avis"
            const panel = document.querySelector(`.filter-panel[data-type="${type}"]`);
            const overlay = document.querySelector('.filter-overlay') || createOverlay();
            const table = document.querySelector(`.${type}-table`);
            const select = panel.querySelector('select');
            const applyBtn = panel.querySelector('.apply-filters');
            const resetBtn = panel.querySelector('.reset-filters');
            const closePanelBtn = panel.querySelector('.close-panel');

            if (!panel || !table || !select || !applyBtn || !resetBtn || !closePanelBtn) return;

            // Ouvrir panel
            toggleBtn.addEventListener('click', e => {
                e.stopPropagation();
                panel.classList.add('active');
                overlay.classList.add('active');
            });

            // Fermer panel
            function closePanel() {
                panel.classList.remove('active');
                overlay.classList.remove('active');
            }
            closePanelBtn.addEventListener('click', e => { e.stopPropagation(); closePanel(); });
            overlay.addEventListener('click', e => { e.stopPropagation(); closePanel(); });

            // Appliquer filtre AJAX
            applyBtn.addEventListener('click', e => {
                e.preventDefault();
                loadFilteredList(type, select.value, table);
                closePanel();
            });

            // Réinitialiser filtre AJAX
            resetBtn.addEventListener('click', e => {
                e.preventDefault();
                select.value = '';
                loadFilteredList(type, '', table);
                closePanel();
            });
        });
    }

    function createOverlay() {
        const overlay = document.createElement('div');
        overlay.classList.add('filter-overlay');
        document.body.appendChild(overlay);
        return overlay;
    }

    function loadFilteredList(type, statutId = '', table) {
        const toggle = document.querySelector(`.filter-toggle[data-type="${type}"]`);
        if (!toggle) return;
        const url = new URL(toggle.dataset.url, window.location.origin);
        if (statutId) url.searchParams.set(type === 'commande' ? 'statutId' : 'statut', statutId);
        url.searchParams.set('ajax', '1');

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(resp => resp.text())
            .then(html => {
                const temp = document.createElement('div');
                temp.innerHTML = html;
                const newTbody = temp.querySelector(`.${type}-table tbody`);
                if (newTbody) table.querySelector('tbody').innerHTML = newTbody.innerHTML;
                // Réattacher filtres si besoin
                attachFilters();
                if(type === 'commande') attachAnnulationFields();
            })
            .catch(err => console.error('Erreur AJAX :', err));
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
            if (selected === 'Annulée') {
                motifField?.classList.remove('hidden');
                contactField?.classList.remove('hidden');
            } else {
                motifField?.classList.add('hidden');
                contactField?.classList.add('hidden');
            }
        }

        toggleAnnulationFields();
        statutSelect?.addEventListener('change', toggleAnnulationFields);
    }

    // Initial attach filters
    attachFilters();

});