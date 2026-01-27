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
 

});