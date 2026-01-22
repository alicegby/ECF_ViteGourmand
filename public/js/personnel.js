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
    // SÉLECTION PERSONNEL
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
        const quantityInput = quantityControls.querySelector('input[name^="personnel_"][name$="_quantite"]');
        const hoursInput = quantityControls.querySelector('input[name^="personnel_"][name$="_heures"]');
        const qtyDecreaseBtn = quantityControls.querySelector('label:nth-of-type(1) .decrease-btn');
        const qtyIncreaseBtn = quantityControls.querySelector('label:nth-of-type(1) .increase-btn');
        const hoursDecreaseBtn = quantityControls.querySelector('label:nth-of-type(2) .decrease-btn');
        const hoursIncreaseBtn = quantityControls.querySelector('label:nth-of-type(2) .increase-btn');
        const validateBtn = quantityControls.querySelector('.options-validate-btn');
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
                if (!card.dataset.validated) {
                    checkIcon.style.display = 'none';
                }
            } else {
                card.classList.add('selected');
                selectBtn.textContent = 'Sélectionné';
                quantityControls.style.display = 'flex';
                quantityInput.value = 1;
                if (card.dataset.validated === 'true') {
                    checkIcon.style.display = 'block';
                }
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

        // Quantité
        qtyIncreaseBtn.addEventListener('click', () => {
            let qty = parseInt(quantityInput.value, 10);
            if (qty < stock) {
                quantityInput.value = qty + 1;
                stockError.style.display = 'none';
                invalidateValidation();
            } else {
                stockError.textContent = 'Stock insuffisant';
                stockError.style.display = 'block';
            }
        });

        qtyDecreaseBtn.addEventListener('click', () => {
            let qty = parseInt(quantityInput.value, 10);
            if (qty > minCommande) {
                quantityInput.value = qty - 1;
                stockError.style.display = 'none';
                invalidateValidation();
            }
        });

        // Heures
        hoursIncreaseBtn.addEventListener('click', () => {
            let hours = parseInt(hoursInput.value, 10);
            hoursInput.value = hours + 1;
            invalidateValidation();
        });

        hoursDecreaseBtn.addEventListener('click', () => {
            let hours = parseInt(hoursInput.value, 10);
            if (hours > 1) hoursInput.value = hours - 1;
            invalidateValidation();
        });

        // ==================================================
        // VALIDER (SEULE ACTION QUI ENREGISTRE)
        // ==================================================
        if (validateBtn) {
            validateBtn.addEventListener('click', () => {

                const qty = parseInt(quantityInput.value, 10);
                const hours = parseInt(hoursInput.value, 10);

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

                fetch('/save-personnel-session', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify([{
                        id: card.dataset.personnelId,
                        qty: qty,
                        hours: hours
                    }])
                })
                .then(() => {
                    sessionStorage.setItem('personnelAdded', 'true');
                    if (typeof updateCartBadge === 'function') updateCartBadge();
                })
                .catch(() => {
                    alert('Erreur lors de la validation du personnel.');
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
            window.location.href = '/panier';
            return;
        }

        const notValidatedCards = selectedCards.filter(
            card => card.dataset.validated !== 'true'
        );

        if (notValidatedCards.length > 0) {
            alert(
                "⚠️ Certaines équipes de professionnels ont été sélectionnées mais ne sont pas validées.\n\n" +
                "Cliquez sur « Valider » pour les ajouter au panier\n" +
                "ou désélectionnez-les avant de continuer."
            );
            return;
        }

        window.location.href = '/panier';
    });

    // ==================================================
    // APPLICATION DES FILTRES
    // ==================================================
    applyBtn.addEventListener('click', () => {
        const selectedCategories = Array.from(filterOptions)
            .filter(o => o.classList.contains('selected'))
            .map(o => o.dataset.id);

        optionCards.forEach(card => {
            const cardCategory = card.dataset.personnelCategoryId;
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