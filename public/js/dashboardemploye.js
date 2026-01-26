document.addEventListener('DOMContentLoaded', function() {

    // Configuration Globale
    const dashboardContent = document.querySelector('.dashboard-content');
    const defaultUrl = dashboardContent.dataset.defaultUrl;

    // Overlay global unique pour tous les filtres 
    const overlay = document.querySelector('.filter-overlay') || createOverlay();

    function createOverlay() {
        const o = document.createElement('div');
        o.classList.add('filter-overlay');
        document.body.appendChild(o);
        return o;
    }

    // Chargement initial si URL par défaut 
    if (defaultUrl) loadAjaxContent(defaultUrl);

    // Gestion des thèmes / sous-menus
    document.querySelectorAll('.theme-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = btn.dataset.target;
            const url = btn.dataset.url;

            // Sous-menu
            if (targetId) {
                const submenu = document.getElementById(targetId);
                if (!submenu) return;
                submenu.classList.toggle('active');
                const arrow = btn.querySelector('.arrow');
                if (arrow) arrow.classList.toggle('rotated');
                return;
            }

            // Bouton simple
            if (url) loadAjaxContent(url);
        });
    });

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

    // Chargement bouton par défaut 
    const defaultBtn = document.querySelector('.ajax-btn[data-default="true"]');
    if (defaultBtn) {
        defaultBtn.classList.add('active');
        loadAjaxContent(defaultBtn.dataset.url);
    }

    // Fonction AJAX globale 
    function loadAjaxContent(url) {
        if (!url) return;

        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(resp => {
                const contentType = resp.headers.get('content-type');
                if (contentType && contentType.includes('application/json')) return resp.json();
                else return resp.text();
            })
            .then(data => { 
                if (typeof data === 'object') {
                    if (data.formHtml) dashboardContent.innerHTML = data.formHtml;
                    if (data.success && data.message) alert(data.message);
                    if (data.redirectUrl) loadAjaxContent(data.redirectUrl);
                    if (data.errors) alert('Erreurs :\n' + data.errors.join('\n'));
                } else {
                    dashboardContent.innerHTML = data;
                }

                // Ré-attachement des formulaires et filtres
                attachFilters();
                attachMenuFormAjax();
                attachPlatFormAjax();
                attachConditionFormAjax();

                const statsContainer = dashboardContent.querySelector('.stats-list');
                if (statsContainer) {
                    dashboardContent.dispatchEvent(new CustomEvent('statsContentLoaded', { detail: { container: statsContainer } }));
                }
            })
            .catch(err => console.error('Erreur AJAX:', err));
    }

    // Filtres génériques 
    function attachFilters() {
        document.querySelectorAll('.filter-toggle').forEach(toggleBtn => {
            const type = toggleBtn.dataset.type;
            const panel = document.querySelector(`.filter-panel[data-type="${type}"]`);
            const table = document.querySelector(`.${type}-table`);
            if (!panel || !table) return;

            const applyBtn = panel.querySelector('.apply-filters');
            const resetBtn = panel.querySelector('.reset-filters');
            const closePanelBtn = panel.querySelector('.close-panel');
            if (!applyBtn || !resetBtn || !closePanelBtn) return;

            // Supprimer les anciens listeners pour éviter doublons
            toggleBtn.onclick = null;
            applyBtn.onclick = null;
            resetBtn.onclick = null;
            closePanelBtn.onclick = null;

            // Ouvrir panel
            toggleBtn.onclick = () => { panel.classList.add('active'); overlay.classList.add('active'); };

            // Fermer panel
            closePanelBtn.onclick = overlay.onclick = () => { panel.classList.remove('active'); overlay.classList.remove('active'); };

            // Appliquer filtre
            applyBtn.onclick = e => {
                e.preventDefault();
                applyFilter(type, panel, toggleBtn, table);
                panel.classList.remove('active');
                overlay.classList.remove('active');
            };

            // Réinitialiser filtre
            resetBtn.onclick = e => {
                e.preventDefault();
                resetFilter(type, panel, toggleBtn, table);
                panel.classList.remove('active');
                overlay.classList.remove('active');
            };
        });
    }

    // Filtres panel
    function applyFilter(type, panel, toggleBtn, table) {
        const urlObj = new URL(toggleBtn.dataset.url, window.location.origin);
        panel.querySelectorAll('select, input').forEach(el => {
            if (el.value) urlObj.searchParams.set(el.name, el.value);
        });
        urlObj.searchParams.set('ajax', '1');

        fetch(urlObj, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(resp => resp.text())
            .then(html => {
                const temp = document.createElement('div');
                temp.innerHTML = html;
                const newTbody = temp.querySelector(`.${type}-table tbody`);
                if (newTbody) table.querySelector('tbody').innerHTML = newTbody.innerHTML;
            })
            .catch(err => console.error('Erreur filtre AJAX:', err));
    }

    function resetFilter(type, panel, toggleBtn, table) {
        panel.querySelectorAll('select, input').forEach(el => el.value = '');
        applyFilter(type, panel, toggleBtn, table);
    }

    // Formulaires AJAX 
    function attachMenuFormAjax() {
        const form = document.querySelector('.ajax-menu-form');
        if (!form || form.dataset.listenerAttached === 'true') return;
        form.dataset.listenerAttached = 'true';

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const action = form.action;
            if (!action) return console.error('Formulaire sans action !');

            const formData = new FormData(form);

            fetch(action, {
                method: (form.method || 'POST').toUpperCase(),
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(resp => resp.json())
            .then(data => {
                if (data.success) {
                    alert(data.message || 'Menu créé avec succès !');
                    if (data.redirectUrl) loadAjaxContent(data.redirectUrl);
                    else document.querySelector('.ajax-btn[data-url*="menu/list"]')?.click();
                } else if (data.errors) alert('Erreur :\n' + data.errors.join('\n'));
            })
            .catch(err => { console.error(err); alert('Erreur lors de l\'envoi du formulaire'); });
        });
    }

    function attachConditionFormAjax() {
        const form = document.querySelector('.ajax-condition-form');
        if (!form || form.dataset.listenerAttached === 'true') return;
        form.dataset.listenerAttached = 'true';

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(resp => resp.json())
            .then(data => {
                if (data.success) alert(data.message);
                document.querySelector('.ajax-btn[data-url*="condition"]')?.click();
            })
            .catch(err => console.error(err));
        });
    }

    function attachPlatFormAjax() {
        ['.ajax-plats-form', '.ajax-plat-form'].forEach(selector => {
            const form = document.querySelector(selector);
            if (!form || form.dataset.listenerAttached === 'true') return;
            form.dataset.listenerAttached = 'true';

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(form);
                fetch(form.action, {
                    method: form.method || 'POST',
                    body: formData,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(resp => resp.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message || 'Opération réussie !');
                        if (data.redirectUrl) loadAjaxContent(data.redirectUrl);
                    } else if (data.errors) alert('Erreurs :\n' + data.errors.join('\n'));
                })
                .catch(err => { console.error(err); alert("Erreur lors de l'envoi du formulaire."); });
            });
        });
    }

});