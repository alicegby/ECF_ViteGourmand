document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editCommandeForm');
    const totalEl = document.getElementById('totalGeneral');
    const remiseEl = document.getElementById('remise');
    const deleteBtn = document.getElementById('deleteCommandeBtn');
    const deleteToken = deleteBtn?.dataset.token;
    const deleteUrl = deleteBtn?.dataset.url;
    const redirectUrl = deleteBtn?.dataset.redirect || '/dashboard'; 

    // ---------------- Validation minimum côté client ----------------
    function validateMinOptions() {
        const errors = [];

        form.querySelectorAll('input[type="number"]').forEach(input => {
            const min = parseInt(input.dataset.min || 0, 10);
            const val = parseInt(input.value || 0, 10);

            // Gestion spécifique personnel : qty * hours
            if (input.name.startsWith('personnel_hours')) {
                const id = input.name.match(/\d+/)[0];
                const qty = parseInt(form.querySelector(`input[name="personnel_qty[${id}]"]`)?.value || 0);
                const hours = val;
                if (qty * hours < min) {
                    const label = input.closest('.option-info')?.querySelector('p')?.innerText || 'Personnel';
                    errors.push(`${label} : minimum ${min}`);
                }
            } else if (input.name.startsWith('personnel_qty')) {
                // déjà vérifié avec hours
                return;
            } else if (val < min) {
                const label = input.closest('.option-info')?.querySelector('p')?.innerText || 'Option';
                errors.push(`${label} : minimum ${min}`);
            }
        });

        // Vérification nbPersonne minimum menu
        const nbPersonneInput = form.querySelector('input[name="nbPersonne"]');
        const nbPersonneVal = parseInt(nbPersonneInput?.value || 0, 10);
        const nbPersMin = parseInt(nbPersonneInput?.min || 0, 10);
        if (nbPersonneVal < nbPersMin) {
            errors.push(`Nombre minimum de personnes pour ce menu : ${nbPersMin}`);
        }

        return errors;
    }

    // ---------------- Calcul total AJAX ----------------
    let calculateTimeout;
    function calculateTotal() {
        const errors = validateMinOptions();
        if (errors.length) {
            totalEl.textContent = '--';
            remiseEl.textContent = '--';
            return;
        }

        if (calculateTimeout) clearTimeout(calculateTimeout);
        calculateTimeout = setTimeout(() => {
            const formData = new FormData(form);
            formData.append('ajax', '1');

            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    totalEl.textContent = parseFloat(data.prixTotal).toFixed(2) + ' €';
                    remiseEl.textContent = parseFloat(data.remise).toFixed(2) + ' €';
                } else if (data.errors) {
                    totalEl.textContent = '--';
                    remiseEl.textContent = '--';
                    console.warn('Validation serveur :', data.errors.join(', '));
                }
            })
            .catch(err => {
                console.error('Erreur AJAX total :', err);
                totalEl.textContent = '--';
                remiseEl.textContent = '--';
            });
        }, 200);
    }

    // Recalcul total à chaque changement
    form.querySelectorAll('.modify-options input[type="number"], input[name="nbPersonne"]')
        .forEach(input => input.addEventListener('input', calculateTotal));

    calculateTotal(); // calcul initial

    // ---------------- Soumission AJAX pour mise à jour ----------------
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        const errors = validateMinOptions();
        if (errors.length) {
            alert('Veuillez corriger les erreurs suivantes :\n' + errors.join('\n'));
            return;
        }

        const formData = new FormData(form);
        formData.append('ajax', '1');
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        then(res => res.json())
        .then(data => {
            if (data.success) {
                totalEl.textContent = parseFloat(data.prixTotal).toFixed(2) + ' €';
                remiseEl.textContent = parseFloat(data.remise).toFixed(2) + ' €';
                alert('Commande mise à jour avec succès !');
            } else if (data.errors) {
                alert('Erreurs serveur :\n' + data.errors.join('\n'));
            }
        })
        .catch(err => {
            console.error(err);
            alert('Impossible de mettre à jour la commande.');
        });
    });

    // ---------------- Annulation commande AJAX ----------------
    if (!deleteBtn) return;

    deleteBtn.addEventListener('click', async function(e) {
        e.preventDefault();

        const url = deleteBtn.dataset.url;
        const token = deleteBtn.dataset.token;

        if (!confirm('Êtes-vous sûre de vouloir annuler cette commande ?')) return;

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `_token=${encodeURIComponent(token)}`
            });

            const text = await response.text();
            console.log('HTTP status:', response.status);
            console.log('Response text:', text);

            let data;
            try {
                data = JSON.parse(text);
            } catch(e) {
                console.error('JSON parse error:', e);
                alert('Erreur : réponse serveur invalide. Voir console pour détails.');
                return;
            }

            if (data.success) {
                alert('Commande annulée avec succès !');
                window.location.href = '/dashboard';
            } else {
                alert('Erreur : ' + (data.message || 'Impossible d’annuler la commande.'));
            }

        } catch (err) {
            console.error(err);
            alert('Erreur réseau ou serveur, impossible d’annuler la commande.');
        }
    });
});