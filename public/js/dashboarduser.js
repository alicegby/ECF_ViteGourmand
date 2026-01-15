document.addEventListener('DOMContentLoaded', function() {
    // ===== FORMULAIRE UTILISATEUR (inchangé) =====
    const editBtn = document.getElementById('edit-user-btn');
    const closeBtn = document.getElementById('close-user-form');
    const displayDiv = document.getElementById('user-info-display');
    const formDiv = document.getElementById('user-info-form');
    const form = formDiv.querySelector('form');

    if (editBtn && closeBtn && displayDiv && formDiv && form) {
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

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const data = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                body: data,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.json())
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

    // ===== COMMANDES EN COURS, TERMINÉES ET GAMIFIÉES =====
    const allCommandeHeaders = document.querySelectorAll('.commande-item .commande-header');

    allCommandeHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const details = this.nextElementSibling;
            if (!details) return;

            // Toggle affichage détails
            if (details.style.display === 'block') {
                details.style.display = 'none';
            } else {
                details.style.display = 'block';
            }

            // Rotation flèche
            const arrow = this.querySelector('.arrow');
            if (arrow) {
                if (arrow.style.transform === 'rotate(90deg)') {
                    arrow.style.transform = 'rotate(0deg)';
                } else {
                    arrow.style.transform = 'rotate(90deg)';
                }
            }
        });
    });
});