document.addEventListener('DOMContentLoaded', () => {
    // -------------------------
    // RÉINITIALISATION DU PANIER
    // -------------------------
    const resetForm = document.querySelector('.panier-reset-form');
    const panierContainer = document.querySelector('.panier-left');

    if (resetForm && panierContainer) {
        resetForm.addEventListener('submit', async e => {
            e.preventDefault();

            try { 
                const res = await fetch(resetForm.action, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ reset: true })
                });

                if (!res.ok) throw new Error('Erreur lors de la réinitialisation du panier');

                // On vide le panier
                panierContainer.innerHTML = '<p class="empty">Votre panier est vide.</p>';

                // Mettre à jour le badge panier si nécessaire
                if (typeof updateCartBadge === 'function') updateCartBadge();

                // Masquer les messages de conditions
                const menuConditionsBlocks = document.querySelectorAll('.menu-conditions ul');
                menuConditionsBlocks.forEach(ul => {
                    ul.style.display = 'none'; // Masque tous les messages
                });

                // Laisser visibles uniquement les inputs date de livraison
                const dateInputs = document.querySelectorAll('.menu-conditions input[type="date"]');
                dateInputs.forEach(input => {
                    input.closest('div').style.display = 'block'; 
                });

            } catch (err) {
                console.error(err);
                alert('Impossible de réinitialiser le panier pour le moment.');
            }

            // Supprimer les messages flash
            const flashMessages = document.querySelectorAll('.flash');
            flashMessages.forEach(flash => flash.remove());
        });
    }

    // -------------------------
    // HEURES FIXES DE LIVRAISON
    // -------------------------
    const heureInput = document.getElementById('heureLivraison');
    const heureText = document.getElementById('heureDisponibles');

    if (heureInput && heureText) {
        heureInput.min = '10:00';
        heureInput.max = '18:00';
        heureText.textContent = 'Heures disponibles : 10:00 - 18:00';
    }
});