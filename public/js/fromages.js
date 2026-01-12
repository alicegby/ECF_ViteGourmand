document.addEventListener('DOMContentLoaded', () => {

    const optionCards = document.querySelectorAll('.options-card');
    const nextBtn = document.querySelector('.next-option-btn');

    const updateNextButton = () => {
        const anySelected = Array.from(optionCards).some(card => card.classList.contains('selected'));
        nextBtn.textContent = anySelected ? 'Suivant' : 'Passer';
        const oldArrow = nextBtn.querySelector('.next-icon');
        if (oldArrow) oldArrow.remove();
        const arrow = document.createElement('img');
        arrow.src = '/Visuels/Icônes/Flèche_Beige.png';
        arrow.className = 'next-icon';
        nextBtn.appendChild(arrow);
    };

    optionCards.forEach(card => {
        const selectBtn = card.querySelector('.options-select-btn');
        const quantityControls = card.querySelector('.options-quantity-controls');
        const decreaseBtn = quantityControls.querySelector('.decrease-btn');
        const increaseBtn = quantityControls.querySelector('.increase-btn');
        const quantityInput = quantityControls.querySelector('.quantity-input');
        const validateBtn = card.querySelector('.options-validate-btn');
        const stockError = quantityControls.querySelector('.options-stock-error');

        const minCommande = parseInt(card.dataset.min, 10);
        const stock = parseInt(card.dataset.stock, 10);

        // Icône ✔
        const checkIcon = document.createElement('span');
        checkIcon.className = 'selected-icon';
        checkIcon.innerHTML = '✔';
        checkIcon.style.position = 'absolute';
        checkIcon.style.top = '10px';
        checkIcon.style.right = '10px';
        checkIcon.style.fontSize = '1.5rem';
        checkIcon.style.color = '#F6F1EB';
        checkIcon.style.display = 'none';
        card.style.position = 'relative';
        card.appendChild(checkIcon);

        quantityControls.style.display = 'none';
        stockError.style.display = 'none';

        // Sélection
        selectBtn.addEventListener('click', () => {
            if (card.classList.contains('selected')) {
                card.classList.remove('selected');
                delete card.dataset.validated;
                checkIcon.style.display = 'none';
                selectBtn.textContent = 'Sélectionner';
                quantityControls.style.display = 'none';
            } else {
                card.classList.add('selected');
                selectBtn.textContent = 'Sélectionné';
                quantityControls.style.display = 'flex';
                quantityInput.value = minCommande;
            }
            updateNextButton();
        });

        // Gestion quantité
        const invalidateValidation = () => { delete card.dataset.validated; checkIcon.style.display = 'none'; };

        increaseBtn.addEventListener('click', () => {
            let qty = parseInt(quantityInput.value, 10);
            if (qty < stock) { quantityInput.value = qty + 1; stockError.style.display = 'none'; invalidateValidation(); }
            else { stockError.textContent = 'Stock insuffisant'; stockError.style.display = 'block'; }
        });

        decreaseBtn.addEventListener('click', () => {
            let qty = parseInt(quantityInput.value, 10);
            if (qty > minCommande) { quantityInput.value = qty - 1; stockError.style.display = 'none'; invalidateValidation(); }
        });

        // Valider
        if (validateBtn) {
            validateBtn.addEventListener('click', () => {
                const qty = parseInt(quantityInput.value, 10);
                if (qty < minCommande) { stockError.textContent = `Minimum ${minCommande} unité(s)`; stockError.style.display = 'block'; return; }
                if (qty > stock) { stockError.textContent = 'Stock insuffisant'; stockError.style.display = 'block'; return; }
                stockError.style.display = 'none';

                card.dataset.validated = 'true';
                checkIcon.style.display = 'block';

                // ✅ Récupérer toutes les cartes validées
                const validatedFromages = Array.from(optionCards)
                    .filter(c => c.dataset.validated === 'true')
                    .map(c => ({
                        id: c.dataset.fromageId,
                        qty: parseInt(c.querySelector('.quantity-input').value, 10)
                    }));

                fetch('/save-fromages-session', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(validatedFromages)
                })
                .then(() => { if (typeof updateCartBadge === 'function') updateCartBadge(); })
                .catch(() => alert('Erreur lors de la validation du fromage.'));
            });
        }
    });

    nextBtn.addEventListener('click', () => {
        const selectedCards = Array.from(optionCards).filter(c => c.classList.contains('selected'));
        const notValidated = selectedCards.filter(c => c.dataset.validated !== 'true');
        if (notValidated.length > 0) {
            alert("⚠️ Certains fromages ont été sélectionnés mais ne sont pas validés.\nCliquez sur « Valider » pour les ajouter au panier ou désélectionnez-les.");
            return;
        }
        window.location.href = '/boissons';
    });

    updateNextButton();
});