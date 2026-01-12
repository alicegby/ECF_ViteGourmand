document.addEventListener('DOMContentLoaded', function() {
    const editBtn = document.getElementById('edit-user-btn');
    const displayDiv = document.getElementById('user-info-display');
    const formDiv = document.getElementById('user-info-form');
    const form = document.getElementById('inline-user-form');

    // Affiche le formulaire inline
    editBtn.addEventListener('click', () => {
        displayDiv.style.display = 'none';
        formDiv.style.display = 'block';
    });

    // Soumission AJAX
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
                    <p><strong>Email :</strong> ${result.user.email}</p>
                    <p><strong>Téléphone :</strong> ${result.user.telephone}</p>
                    <p><strong>Adresse :</strong> ${result.user.adressePostale}, ${result.user.codePostal} ${result.user.ville}</p>
                `;
                formDiv.style.display = 'none';
                displayDiv.style.display = 'block';
            } else {
                alert('Erreur : ' + result.message);
            }
        })
        .catch(err => console.error(err));
    });
});