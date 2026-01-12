document.addEventListener('DOMContentLoaded', () => {

    // ==================================================
    // FILTRES
    // ==================================================
    const filterToggle = document.querySelector('.options-filter-toggle');
    const filterPanel = document.querySelector('.options-filter-panel');
    const filterOverlay = document.querySelector('.options-filter-overlay');
    const closePanel = document.querySelector('.options-close-panel');
    const filterOptions = document.querySelectorAll('.options-filter-option');
    const applyBtn = document.querySelector('.options-apply-filters');
    const resetBtn = document.querySelector('.options-reset-filters');

    const openFilter = () => {
        filterPanel.classList.add('active');
        filterOverlay.classList.add('active');
    };

    const closeFilter = () => {
        filterPanel.classList.remove('active');
        filterOverlay.classList.remove('active');
    };

    filterToggle.addEventListener('click', openFilter);
    closePanel.addEventListener('click', closeFilter);
    filterOverlay.addEventListener('click', closeFilter);

    filterOptions.forEach(option => {
        option.addEventListener('click', () => {
            option.classList.toggle('selected');
        });
    });

    // ==================================================
    // SÉLECTION MATERIEL
    // ==================================================
    const optionCards = document.querySelectorAll('.options-card');
    const nextBtn = document.querySelector('.next-option-btn');

    const updateNextButton = () => {
        const anySelected = Array.from(optionCards).some(card =>
            card.classList.contains('selected')
        );

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

        const minCommande = parseInt(card.dataset.min || 1, 10);
        const stock = parseInt(card.dataset.stock, 10);

        // ✔ icône validation
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

        stockError.style.display = 'none';
        quantityControls.style.display = 'none';

        // ==================================================
        // SÉLECTION / DÉSÉLECTION
        // ==================================================
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
                quantityInput.value = 1;
            }

            updateNextButton();
        });

        // ==================================================
        // GESTION DES QUANTITÉS (annule validation)
        // ==================================================
        const invalidateValidation = () => {
            delete card.dataset.validated;
            checkIcon.style.display = 'none';
        };

        increaseBtn.addEventListener('click', () => {
            const qty = parseInt(quantityInput.value, 10);
            if (qty < stock) {
                quantityInput.value = qty + 1;
                stockError.style.display = 'none';
                invalidateValidation();
            } else {
                stockError.textContent = 'Stock insuffisant';
                stockError.style.display = 'block';
            }
        });

        decreaseBtn.addEventListener('click', () => {
            const qty = parseInt(quantityInput.value, 10);
            if (qty > minCommande) {
                quantityInput.value = qty - 1;
                stockError.style.display = 'none';
                invalidateValidation();
            }
        });

        // ==================================================
        // VALIDER (SEULE ACTION QUI ENREGISTRE)
        // ==================================================
        if (validateBtn) {
            validateBtn.addEventListener('click', () => {

                const qty = parseInt(quantityInput.value, 10);

                if (qty < minCommande) {
                    stockError.textContent = `Minimum ${minCommande} unité(s)`;
                    stockError.style.display = 'block';
                    return;
                }

                if (qty > stock) {
                    stockError.textContent = 'Stock insuffisant';
                    stockError.style.display = 'block';
                    return;
                }

                stockError.style.display = 'none';

                card.dataset.validated = 'true';
                checkIcon.style.display = 'block';

                fetch('/save-materiel-session', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify([{
                        id: card.dataset.materielId,
                        qty: qty
                    }])
                })
                .then(() => {
                    sessionStorage.setItem('materielAdded', 'true');
                    if (typeof updateCartBadge === 'function') updateCartBadge();
                })
                .catch(() => {
                    alert('Erreur lors de la validation du matériel.');
                });
            });
        }
    });

    // ==================================================
    // SUIVANT / PASSER
    // ==================================================
    nextBtn.addEventListener('click', () => {

        const selectedCards = Array.from(optionCards)
            .filter(card => card.classList.contains('selected'));

        if (selectedCards.length === 0) {
            window.location.href = '/personnel';
            return;
        }

        const notValidatedCards = selectedCards.filter(
            card => card.dataset.validated !== 'true'
        );

        if (notValidatedCards.length > 0) {
            alert(
                "⚠️ Certains matériels ont été sélectionnés mais ne sont pas validés.\n\n" +
                "Cliquez sur « Valider » pour les ajouter au panier\n" +
                "ou désélectionnez-les avant de continuer."
            );
            return;
        }

        window.location.href = '/personnel';
    });

    // ==================================================
    // APPLICATION DES FILTRES
    // ==================================================
    applyBtn.addEventListener('click', () => {
        const selectedCategories = Array.from(filterOptions)
            .filter(o => o.classList.contains('selected'))
            .map(o => o.dataset.id);

        optionCards.forEach(card => {
            const cardCategory = card.dataset.materielCategoryId;
            card.style.display =
                selectedCategories.length === 0 ||
                selectedCategories.includes(cardCategory)
                    ? 'flex'
                    : 'none';
        });

        closeFilter();
    });

    resetBtn.addEventListener('click', () => {
        filterOptions.forEach(o => o.classList.remove('selected'));
        optionCards.forEach(card => card.style.display = 'flex');
    });

    updateNextButton();
});