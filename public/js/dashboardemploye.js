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

    // Chargement AJAX global
    function loadAjaxContent(url) {
        if (!url) return;
        fetch(url)
            .then(resp => resp.text())
            .then(html => {
                dashboardContent.innerHTML = html;
                attachFilters();        // rattache tous les filtres après chargement
                attachAnnulationFields(); 
                attachMenuFormAjax();
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
        const type = toggleBtn.dataset.type; // commande, avis, menu etc
        const panel = document.querySelector(`.filter-panel[data-type="${type}"]`);
        const overlay = document.querySelector('.filter-overlay') || createOverlay();
        const table = document.querySelector(`.${type}-table`);

        if (!panel || !table) return;

        const applyBtn = panel.querySelector('.apply-filters');
        const resetBtn = panel.querySelector('.reset-filters');
        const closePanelBtn = panel.querySelector('.close-panel');

        if (!applyBtn || !resetBtn || !closePanelBtn) return;

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

        // Filtrage AJAX spécifique menu
        if (type === 'menu') {
            applyBtn.addEventListener('click', e => {
                e.preventDefault();

                const theme = panel.querySelector('select[name="theme"]').value;
                const regime = panel.querySelector('select[name="regime"]').value;
                const keyword = panel.querySelector('input[name="keyword"]').value;

                const urlObj = new URL(toggleBtn.dataset.url, window.location.origin);
                if (theme) urlObj.searchParams.set('theme', theme);
                if (regime) urlObj.searchParams.set('regime', regime);
                if (keyword) urlObj.searchParams.set('keyword', keyword);
                urlObj.searchParams.set('ajax', '1');

                fetch(urlObj, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(resp => resp.text())
                    .then(html => {
                        const temp = document.createElement('div');
                        temp.innerHTML = html;
                        const newTbody = temp.querySelector(`.${type}-table tbody`);
                        if (newTbody) table.querySelector('tbody').innerHTML = newTbody.innerHTML;
                    });

                closePanel();
            });

            resetBtn.addEventListener('click', e => {
                e.preventDefault();
                panel.querySelector('select[name="theme"]').value = '';
                panel.querySelector('select[name="regime"]').value = '';
                panel.querySelector('input[name="keyword"]').value = '';

                const urlObj = new URL(toggleBtn.dataset.url, window.location.origin);
                urlObj.searchParams.set('ajax', '1');

                fetch(urlObj, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(resp => resp.text())
                    .then(html => {
                        const temp = document.createElement('div');
                        temp.innerHTML = html;
                        const newTbody = temp.querySelector(`.${type}-table tbody`);
                        if (newTbody) table.querySelector('tbody').innerHTML = newTbody.innerHTML;
                    });

                closePanel();
            });
        } else {
            // Pour les autres panels (commande, avis), garde l'ancien code
            const select = panel.querySelector('select');
            if (!select) return;

            applyBtn.addEventListener('click', e => {
                e.preventDefault();
                loadFilteredList(type, select.value, table);
                closePanel();
            });

            resetBtn.addEventListener('click', e => {
                e.preventDefault();
                select.value = '';
                loadFilteredList(type, '', table);
                closePanel();
            });
        }
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

    // Formulaire Menu Ajax
    function attachMenuFormAjax() {
    const form = document.querySelector('.ajax-menu-form');
    if (!form) return;

    // Évite les duplications de listeners
    if (form.dataset.listenerAttached === 'true') return;
    form.dataset.listenerAttached = 'true';

    console.log('Formulaire menu attaché');

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        console.log('Soumission du formulaire menu...');

        const action = form.getAttribute('action');
        const method = (form.getAttribute('method') || 'POST').toUpperCase();

        if (!action) {
            console.error('Formulaire sans action !');
            return;
        }

        // --- Construction du FormData pour gérer fichiers ---
        const formData = new FormData(form);

        // DEBUG : voir si les fichiers sont bien présents
        for (let [key, value] of formData.entries()) {
            if (value instanceof File && value.name) {
                console.log(`Fichier détecté : ${key} -> ${value.name}`);
            } else {
                console.log(`${key}:`, value);
            }
        }

        // Envoi AJAX
        fetch(action, {
            method: method,
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest' // IMPORTANT pour Symfony
            }
        })
        .then(resp => {
            // Vérifie que le serveur renvoie du JSON
            const contentType = resp.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return resp.json();
            } else {
                return resp.text().then(html => { throw new Error('Réponse non JSON : ' + html); });
            }
        })
        .then(data => {
            console.log('Réponse JSON reçue :', data);

            if (data.success) {
                alert('Menu créé avec succès !');

                // Redirection ou rechargement liste
                if (data.redirectUrl) {
                    loadAjaxContent(data.redirectUrl);
                } else {
                    const listBtn = document.querySelector('.ajax-btn[data-url*="menu/list"]');
                    if (listBtn) listBtn.click();
                }
            } else if (data.errors) {
                console.error('Erreurs de validation :', data.errors);
                alert('Erreur :\n' + data.errors.join('\n'));
            } else {
                console.error('⚠️ Réponse inattendue', data);
                alert('Une erreur est survenue');
            }
        })
        .catch(err => {
            console.error('Erreur AJAX :', err);
            alert('Erreur lors de l\'envoi du formulaire');
        });
    });
}

    // Initial attach filters
    attachFilters();
    attachMenuFormAjax();

});