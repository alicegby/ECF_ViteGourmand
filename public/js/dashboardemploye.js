document.addEventListener('DOMContentLoaded', function() {

    // Gestion des thèmes / sous-menus
    document.querySelectorAll('.theme-btn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();

            const targetId = btn.dataset.target;
            const url = btn.dataset.url;

            // CAS 1 : bouton avec sous-menu
            if (targetId) {
                const submenu = document.getElementById(targetId);
                if (!submenu) return;

                submenu.classList.toggle('active');

                const arrow = btn.querySelector('.arrow');
                if (arrow) arrow.classList.toggle('rotated');
                return;
            }

            // CAS 2 : bouton simple → chargement AJAX
            if (url) {
                loadAjaxContent(url);
            }
        });
    });

    const dashboardContent = document.querySelector('.dashboard-content');
    const defaultUrl = dashboardContent.dataset.defaultUrl;
    if (defaultUrl) {
        loadAjaxContent(defaultUrl);
    }

    // Chargement AJAX global
    function loadAjaxContent(url) {
    if (!url) return;

    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(resp => {
            const contentType = resp.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return resp.json();
            } else {
                return resp.text();
            }
        })
        .then(data => {
            // Cas JSON (édition de commande, succès ou erreurs)
            if (typeof data === 'object') {
                if (data.success) {
                    // Action réussie : message + reload si redirectUrl
                    if (data.message) alert(data.message);
                    if (data.redirectUrl) {
                        loadAjaxContent(data.redirectUrl);
                    }
                } else if (data.formHtml) {
                    // Injecte le formulaire dans le dashboard
                    dashboardContent.innerHTML = data.formHtml;

                    // Rattache les listeners selon le formulaire présent
                    attachFilters();
                    attachCommandFormAjax(); // nouveau listener pour formulaire commande
                    attachMenuFormAjax();
                    attachPlatFormAjax();
                    attachConditionFormAjax();
                } else if (data.errors) {
                    alert('Erreurs :\n' + data.errors.join('\n'));
                }
            } else {
                // Cas HTML pur (liste, tableau, etc.)
                dashboardContent.innerHTML = data;

                // Rattache les listeners aux éléments rechargés
                attachFilters();
                attachMenuFormAjax();
                attachPlatFormAjax();
                attachConditionFormAjax();
            }
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
        } else if (type === 'plats') {
        applyBtn.addEventListener('click', e => {
            e.preventDefault();

            const category = panel.querySelector('select[name="category"]').value;
            const allergene = panel.querySelector('select[name="allergene"]').value;
            const keyword = panel.querySelector('input[name="keyword"]').value;

            const urlObj = new URL(toggleBtn.dataset.url, window.location.origin);

            if (category) urlObj.searchParams.set('category', category);
            if (allergene) urlObj.searchParams.set('allergene', allergene);
            if (keyword) urlObj.searchParams.set('keyword', keyword);

            urlObj.searchParams.set('ajax', '1');

            fetch(urlObj, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(resp => resp.text())
            .then(html => {
                const temp = document.createElement('div');
                temp.innerHTML = html;

                const newTbody = temp.querySelector('.plats-table tbody');
                if (newTbody) {
                    table.querySelector('tbody').innerHTML = newTbody.innerHTML;
                }
            });

            closePanel();
        });

        resetBtn.addEventListener('click', e => {
            e.preventDefault();

            panel.querySelector('select[name="category"]').value = '';
            panel.querySelector('select[name="allergene"]').value = '';
            panel.querySelector('input[name="keyword"]').value = '';

            const urlObj = new URL(toggleBtn.dataset.url, window.location.origin);
            urlObj.searchParams.set('ajax', '1');

            fetch(urlObj, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(resp => resp.text())
            .then(html => {
                const temp = document.createElement('div');
                temp.innerHTML = html;

                const newTbody = temp.querySelector('.plats-table tbody');
                if (newTbody) {
                    table.querySelector('tbody').innerHTML = newTbody.innerHTML;
                }
            });

            closePanel();
        });
    } else if (type === 'menuplat') { // data-type="menuplat" sur le bouton et le panel
        applyBtn.addEventListener('click', e => {
            e.preventDefault();

            const menu = panel.querySelector('select[name="menu"]').value;
            const keyword = panel.querySelector('input[name="keyword"]').value;

            const urlObj = new URL(toggleBtn.dataset.url, window.location.origin);

            if (menu) urlObj.searchParams.set('menu', menu);
            if (keyword) urlObj.searchParams.set('keyword', keyword);

            urlObj.searchParams.set('ajax', '1');

            fetch(urlObj, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(resp => resp.text())
                .then(html => {
                    const temp = document.createElement('div');
                    temp.innerHTML = html;
                    const newTbody = temp.querySelector('.menuplat-table tbody');
                    if (newTbody) table.querySelector('tbody').innerHTML = newTbody.innerHTML;
                });

            closePanel();
        });

        resetBtn.addEventListener('click', e => {
            e.preventDefault();
            panel.querySelector('select[name="menu"]').value = '';
            panel.querySelector('input[name="keyword"]').value = '';

            const urlObj = new URL(toggleBtn.dataset.url, window.location.origin);
            urlObj.searchParams.set('ajax', '1');

            fetch(urlObj, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(resp => resp.text())
                .then(html => {
                    const temp = document.createElement('div');
                    temp.innerHTML = html;
                    const newTbody = temp.querySelector('.menuplat-table tbody');
                    if (newTbody) table.querySelector('tbody').innerHTML = newTbody.innerHTML;
                });

            closePanel();
        });
    }  else if (type === 'fromages') {

        applyBtn.addEventListener('click', e => {
            e.preventDefault();

            const category = panel.querySelector('select[name="category"]').value;
            const keyword = panel.querySelector('input[name="keyword"]').value;

            const urlObj = new URL(toggleBtn.dataset.url, window.location.origin);

            if (category) urlObj.searchParams.set('category', category);
            if (keyword) urlObj.searchParams.set('keyword', keyword);

            urlObj.searchParams.set('ajax', '1');

            fetch(urlObj, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(resp => resp.text())
            .then(html => {
                const temp = document.createElement('div');
                temp.innerHTML = html;

                const newTbody = temp.querySelector('.fromages-table tbody');
                if (newTbody) {
                    table.querySelector('tbody').innerHTML = newTbody.innerHTML;
                }
            });

            closePanel();
        });

        resetBtn.addEventListener('click', e => {
            e.preventDefault();

            panel.querySelector('select[name="category"]').value = '';
            panel.querySelector('input[name="keyword"]').value = '';

            const urlObj = new URL(toggleBtn.dataset.url, window.location.origin);
            urlObj.searchParams.set('ajax', '1');

            fetch(urlObj, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(resp => resp.text())
            .then(html => {
                const temp = document.createElement('div');
                temp.innerHTML = html;

                const newTbody = temp.querySelector('.fromages-table tbody');
                if (newTbody) {
                    table.querySelector('tbody').innerHTML = newTbody.innerHTML;
                }
            });

            closePanel();
        });
    } else if (type === 'boissons') {

        applyBtn.addEventListener('click', e => {
            e.preventDefault();

            const category = panel.querySelector('select[name="category"]').value;
            const keyword = panel.querySelector('input[name="keyword"]').value;

            const urlObj = new URL(toggleBtn.dataset.url, window.location.origin);

            if (category) urlObj.searchParams.set('category', category);
            if (keyword) urlObj.searchParams.set('keyword', keyword);

            urlObj.searchParams.set('ajax', '1');

            fetch(urlObj, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(resp => resp.text())
            .then(html => {
                const temp = document.createElement('div');
                temp.innerHTML = html;

                const newTbody = temp.querySelector('.boissons-table tbody');
                if (newTbody) {
                    table.querySelector('tbody').innerHTML = newTbody.innerHTML;
                }
            });

            closePanel();
        });

        resetBtn.addEventListener('click', e => {
            e.preventDefault();

            panel.querySelector('select[name="category"]').value = '';
            panel.querySelector('input[name="keyword"]').value = '';

            const urlObj = new URL(toggleBtn.dataset.url, window.location.origin);
            urlObj.searchParams.set('ajax', '1');

            fetch(urlObj, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(resp => resp.text())
            .then(html => {
                const temp = document.createElement('div');
                temp.innerHTML = html;

                const newTbody = temp.querySelector('.boissons-table tbody');
                if (newTbody) {
                    table.querySelector('tbody').innerHTML = newTbody.innerHTML;
                }
            });

            closePanel();
        });
    } else if (type === 'materiel') {

        applyBtn.addEventListener('click', e => {
            e.preventDefault();

            const category = panel.querySelector('select[name="category"]').value;
            const keyword = panel.querySelector('input[name="keyword"]').value;

            const urlObj = new URL(toggleBtn.dataset.url, window.location.origin);

            if (category) urlObj.searchParams.set('category', category);
            if (keyword) urlObj.searchParams.set('keyword', keyword);

            urlObj.searchParams.set('ajax', '1');

            fetch(urlObj, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(resp => resp.text())
            .then(html => {
                const temp = document.createElement('div');
                temp.innerHTML = html;

                const newTbody = temp.querySelector('.materiel-table tbody');
                if (newTbody) {
                    table.querySelector('tbody').innerHTML = newTbody.innerHTML;
                }
            });

            closePanel();
        });

        resetBtn.addEventListener('click', e => {
            e.preventDefault();

            panel.querySelector('select[name="category"]').value = '';
            panel.querySelector('input[name="keyword"]').value = '';

            const urlObj = new URL(toggleBtn.dataset.url, window.location.origin);
            urlObj.searchParams.set('ajax', '1');

            fetch(urlObj, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(resp => resp.text())
            .then(html => {
                const temp = document.createElement('div');
                temp.innerHTML = html;

                const newTbody = temp.querySelector('.materiel-table tbody');
                if (newTbody) {
                    table.querySelector('tbody').innerHTML = newTbody.innerHTML;
                }
            });

            closePanel();
        });
    } else if (type === 'personnel') {

        applyBtn.addEventListener('click', e => {
            e.preventDefault();

            const category = panel.querySelector('select[name="category"]').value;
            const keyword = panel.querySelector('input[name="keyword"]').value;

            const urlObj = new URL(toggleBtn.dataset.url, window.location.origin);

            if (category) urlObj.searchParams.set('category', category);
            if (keyword) urlObj.searchParams.set('keyword', keyword);

            urlObj.searchParams.set('ajax', '1');

            fetch(urlObj, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(resp => resp.text())
            .then(html => {
                const temp = document.createElement('div');
                temp.innerHTML = html;

                const newTbody = temp.querySelector('.personnel-table tbody');
                if (newTbody) {
                    table.querySelector('tbody').innerHTML = newTbody.innerHTML;
                }
            });

            closePanel();
        });

        resetBtn.addEventListener('click', e => {
            e.preventDefault();

            panel.querySelector('select[name="category"]').value = '';
            panel.querySelector('input[name="keyword"]').value = '';

            const urlObj = new URL(toggleBtn.dataset.url, window.location.origin);
            urlObj.searchParams.set('ajax', '1');

            fetch(urlObj, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(resp => resp.text())
            .then(html => {
                const temp = document.createElement('div');
                temp.innerHTML = html;

                const newTbody = temp.querySelector('.personnel-table tbody');
                if (newTbody) {
                    table.querySelector('tbody').innerHTML = newTbody.innerHTML;
                }
            });

            closePanel();
        });
    } else if (type === 'client') {

        applyBtn.addEventListener('click', e => {
            e.preventDefault();

            const keyword = panel.querySelector('input[name="keyword"]').value;

            const urlObj = new URL(toggleBtn.dataset.url, window.location.origin);

            if (keyword) urlObj.searchParams.set('keyword', keyword);

            urlObj.searchParams.set('ajax', '1');

            fetch(urlObj, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(resp => resp.text())
            .then(html => {
                const temp = document.createElement('div');
                temp.innerHTML = html;

                const newTbody = temp.querySelector('.client-table tbody');
                if (newTbody) {
                    table.querySelector('tbody').innerHTML = newTbody.innerHTML;
                }
            });

            closePanel();
        });

        resetBtn.addEventListener('click', e => {
            e.preventDefault();

            panel.querySelector('input[name="keyword"]').value = '';

            const urlObj = new URL(toggleBtn.dataset.url, window.location.origin);
            urlObj.searchParams.set('ajax', '1');

            fetch(urlObj, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(resp => resp.text())
            .then(html => {
                const temp = document.createElement('div');
                temp.innerHTML = html;

                const newTbody = temp.querySelector('.client-table tbody');
                if (newTbody) {
                    table.querySelector('tbody').innerHTML = newTbody.innerHTML;
                }
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

    // Fromulaire Condition Ajax
    function attachConditionFormAjax() {
        const form = document.querySelector('.ajax-condition-form');
        if (!form) return;

        if (form.dataset.listenerAttached === 'true') return;
        form.dataset.listenerAttached = 'true';

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(resp => resp.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);

                    // recharge la liste des conditions
                    const btn = document.querySelector('.ajax-btn[data-url*="condition"]');
                    if (btn) btn.click();
                }
            })
            .catch(err => console.error(err));
        });
    }

    // Formulaire Plat Ajax
    function attachPlatFormAjax() {
       const newForm = document.querySelector('.ajax-plats-form');
        if (newForm && newForm.dataset.listenerAttached !== 'true') {
            newForm.dataset.listenerAttached = 'true';
            console.log('Listener AJAX attaché pour NEW plat');
            newForm.addEventListener('submit', handlePlatFormSubmit);
        }

        const editForm = document.querySelector('.ajax-plat-form');
        if (editForm && editForm.dataset.listenerAttached !== 'true') {
            editForm.dataset.listenerAttached = 'true';
            console.log('Listener AJAX attaché pour EDIT plat');
            editForm.addEventListener('submit', handlePlatFormSubmit);
        }
    }

    function attachPlatFormAjax() {
        // NEW Plat
        const newForm = document.querySelector('.ajax-plats-form');
        if (newForm && newForm.dataset.listenerAttached !== 'true') {
            newForm.dataset.listenerAttached = 'true';
            console.log('Listener AJAX attaché pour NEW plat');
            newForm.addEventListener('submit', handlePlatFormSubmit);
        }

        // EDIT Plat
        const editForm = document.querySelector('.ajax-plat-form');
        if (editForm && editForm.dataset.listenerAttached !== 'true') {
            editForm.dataset.listenerAttached = 'true';
            console.log('Listener AJAX attaché pour EDIT plat');
            editForm.addEventListener('submit', handlePlatFormSubmit);
        }
    }

    function handlePlatFormSubmit(e) {
        e.preventDefault();
        const form = e.target;
        const action = form.getAttribute('action');
        if (!action) {
            console.error('Formulaire sans action !');
            return;
        }

        console.log('Soumission AJAX plat', action);

        const formData = new FormData(form);

        fetch(action, {
            method: form.getAttribute('method') || 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(resp => resp.json())
        .then(data => {
            console.log('Réponse JSON reçu plat:', data);

            if (data.success) {
                alert(data.message || 'Opération réussie !');
                if (data.redirectUrl) loadAjaxContent(data.redirectUrl);
            } else if (data.errors) {
                alert('Erreurs :\n' + data.errors.join('\n'));
            }
        })
        .catch(err => {
            console.error('Erreur AJAX formulaire plat :', err);
            alert("Une erreur est survenue lors de l'envoi du formulaire.");
        });
    }

    // Initial attach filters
    attachFilters();
    attachMenuFormAjax();
    attachConditionFormAjax();
    attachPlatFormAjax();

    
});