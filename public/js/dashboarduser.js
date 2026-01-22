document.addEventListener('DOMContentLoaded', function() {

    // ===== FORMULAIRE UTILISATEUR =====
    const editBtn = document.getElementById('edit-user-btn');
    const closeBtn = document.getElementById('close-user-form');
    const displayDiv = document.getElementById('user-info-display');
    const formDiv = document.getElementById('user-info-form');
    const userForm = formDiv ? formDiv.querySelector('form') : null;

    if (editBtn && closeBtn && displayDiv && formDiv && userForm) {
        editBtn.addEventListener('click', () => {
            displayDiv.style.display = 'none';
            formDiv.style.display = 'block';
            editBtn.style.display = 'none';
            closeBtn.style.display = 'inline-block';
        });

        closeBtn.addEventListener('click', () => {
            formDiv.style.display = 'none';
            displayDiv.style.display = 'block';
            editBtn.style.display = 'inline-block';
            closeBtn.style.display = 'none';
        });

        userForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const data = new FormData(userForm);

            fetch(userForm.action, {
                method: 'POST',
                body: data,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(result => {
                if (result.success) {
                    displayDiv.innerHTML = `
                        <p>${result.user.prenom} ${result.user.nom}</p>
                        <p>${result.user.email}</p>
                        <p><strong>Téléphone :</strong> ${result.user.telephone}</p>
                        <p><strong>Adresse :</strong> ${result.user.adressePostale} - ${result.user.codePostal} ${result.user.ville}</p>
                    `;
                    formDiv.style.display = 'none';
                    displayDiv.style.display = 'block';
                    editBtn.style.display = 'inline-block';
                    closeBtn.style.display = 'none';
                } else {
                    alert('Erreur : ' + result.message);
                }
            })
            .catch(err => console.error(err));
        });
    }

    // ===== DÉPLIER / REPLIER LES COMMANDES =====
    document.querySelectorAll('.commande-item .commande-header').forEach(header => {
        header.addEventListener('click', () => {
            const details = header.nextElementSibling;
            if (!details) return;
            details.style.display = details.style.display === 'block' ? 'none' : 'block';
            const arrow = header.querySelector('.arrow');
            if (arrow) arrow.style.transform = arrow.style.transform === 'rotate(90deg)' ? 'rotate(0deg)' : 'rotate(90deg)';
        });
    });

   // ===== BOUTON MODIFIER COMMANDE =====
    document.querySelectorAll('.btn-edit-cmd').forEach(btn => {
        btn.addEventListener('click', async function() {
            const commandeId = this.dataset.commandeId;
            const parentItem = this.closest('.commande-item');
            const formContainer = parentItem.querySelector('.commande-form');

            // toggle formulaire
            if (formContainer.style.display === 'block') {
                formContainer.style.display = 'none';
                return;
            }

            try {
                // charger formulaire via AJAX
                const res = await fetch(`/commande/${commandeId}/edit`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const html = await res.text();

                formContainer.innerHTML = html;
                formContainer.style.display = 'block';

                // ATTACHER SUBMIT
                attachFormSubmit(formContainer, parentItem, commandeId);

                // BOUTON ANNULER COMMANDE
                const cancelBtn = formContainer.querySelector('.btn-annuler-commande');
                if (cancelBtn) {
                    cancelBtn.addEventListener('click', async function() {
                        if (!confirm('Voulez-vous vraiment annuler cette commande ?')) return;
                        try {
                            const res = await fetch(this.dataset.url, {
                                method: 'POST',
                                headers: { 'X-Requested-With': 'XMLHttpRequest' }
                            });
                            const result = await res.json();
                            if (result.success) parentItem.remove();
                            else alert('Erreur : ' + result.message);
                        } catch (err) { console.error(err); }
                    });
                }

            } catch (err) {
                console.error(err);
            }
        });
    });

    // ===== FONCTION ATTACHER SUBMIT AJAX =====
    function attachFormSubmit(formContainer, parentItem, commandeId) {
        const form = formContainer.querySelector('form');
        if (!form) return;

        form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const data = new FormData(form);

            // ajouter les quantités des options
            form.querySelectorAll('.option-item-modify').forEach(item => {
                const inputQuant = item.querySelector('.option-quantite');
                if (inputQuant) data.set(inputQuant.name, inputQuant.value);
            });

            try {
                const res = await fetch(form.action, {
                    method: 'POST',
                    body: data,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const result = await res.json();

                if (result.success) {
                    alert(result.message || 'Commande mise à jour avec succès');
                    formContainer.style.display = 'none';

                    // rafraîchir juste la commande
                    const listRes = await fetch(window.location.href, { headers: { 'X-Requested-With': 'XMLHttpRequest' }});
                    const listHtml = await listRes.text();
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(listHtml, 'text/html');
                    const updatedDetails = doc.querySelector(`.commande-item[data-commande-id="${commandeId}"] .commande-details`);
                    if (updatedDetails) parentItem.querySelector('.commande-details').innerHTML = updatedDetails.innerHTML;

                } else {
                    // formulaire invalide → réinjecter HTML et rattacher submit
                    formContainer.innerHTML = result.formHtml;
                    attachFormSubmit(formContainer, parentItem, commandeId);
                }

            } catch (err) {
                console.error(err);
            }
        });
    }

});