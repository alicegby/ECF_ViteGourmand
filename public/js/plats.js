document.addEventListener('DOMContentLoaded', () => {

    // --- Sélection plat par colonne ---
    document.querySelectorAll('.selection-column').forEach(column => {
        let selectedCard = null;
        column.querySelectorAll('.plat-card').forEach(card => {
            const selectBtn = card.querySelector('.select-plat-btn');

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

            selectBtn.addEventListener('click', () => {
                if (selectedCard === card) {
                    // Désélection
                    card.classList.remove('selected');
                    checkIcon.style.display = 'none';
                    selectBtn.textContent = 'Sélectionner';
                    selectedCard = null;
                    column.querySelectorAll('.plat-card').forEach(c => {
                        c.style.opacity = '1';
                        c.querySelector('.select-plat-btn').textContent = 'Sélectionner';
                    });
                } else {
                    // Nouvelle sélection
                    if (selectedCard) {
                        selectedCard.classList.remove('selected');
                        selectedCard.querySelector('.selected-icon').style.display = 'none';
                    }
                    card.classList.add('selected');
                    checkIcon.style.display = 'block';
                    selectedCard = card;

                    // Mettre les textes corrects
                    column.querySelectorAll('.plat-card').forEach(c => {
                        if (c === card) {
                            c.querySelector('.select-plat-btn').textContent = 'Sélectionné';
                            c.style.opacity = '1';
                        } else {
                            c.querySelector('.select-plat-btn').textContent = 'Modifier';
                            c.style.opacity = '0.5';
                        }
                    });
                }
            });
        });
    });

    // --- Bouton "Suivant" du menu ---
    const nextBtn = document.querySelector('.next-menu-btn');
    const quantityControls = document.querySelector('.quantity-controls');
    const decreaseBtn = quantityControls.querySelector('.decrease-btn');
    const increaseBtn = quantityControls.querySelector('.increase-btn');
    const quantityInput = quantityControls.querySelector('.quantity-input');
    const validateBtn = quantityControls.querySelector('.validate-menu-btn');
    const stockError = document.querySelector('.stock-error');

    const menuStockEl = document.getElementById('menu-stock');
    const menuStock = menuStockEl ? parseInt(menuStockEl.dataset.stock, 10) : 0;
    const minPersons = menuStockEl ? parseInt(menuStockEl.dataset.minPersons, 10) : 1;
    const menuId = menuStockEl ? parseInt(menuStockEl.dataset.menuId, 10) : null;

    quantityControls.style.display = 'none';
    if (stockError) stockError.style.display = 'none';

    nextBtn?.addEventListener('click', () => {
        let allSelected = true;
        document.querySelectorAll('.selection-column').forEach(column => {
            if (!column.querySelector('.plat-card.selected')) allSelected = false;
        });

        if (!allSelected) {
            alert("Veuillez sélectionner un plat dans chaque catégorie avant de continuer.");
            return;
        }

        if (menuStock <= 0) {
            stockError.textContent = "Stock insuffisant.";
            stockError.style.display = 'block';
            return;
        }

        quantityControls.style.display = 'flex';
        stockError.style.display = 'none';
        quantityInput.value = minPersons;
        quantityInput.min = minPersons;
        quantityInput.max = menuStock;
    });

    // --- Boutons + / - ---
    increaseBtn.addEventListener('click', () => {
        let qty = parseInt(quantityInput.value);
        if (qty < menuStock) {
            quantityInput.value = qty + 1;
            stockError.style.display = 'none';
        } else {
            stockError.textContent = "Stock insuffisant.";
            stockError.style.display = 'block';
        }
    });

    decreaseBtn.addEventListener('click', () => {
        let qty = parseInt(quantityInput.value);
        if (qty > minPersons) {
            quantityInput.value = qty - 1;
            stockError.style.display = 'none';
        }
    });

    // --- Validation / AJAX ---
    validateBtn.addEventListener('click', () => {
        const qty = parseInt(quantityInput.value);

        if (qty > menuStock) {
            stockError.textContent = "Stock insuffisant.";
            stockError.style.display = 'block';
            return;
        }

        if (qty < minPersons) {
            stockError.textContent = `La quantité doit être au moins de ${minPersons} personne(s).`;
            stockError.style.display = 'block';
            return;
        }

        stockError.style.display = 'none';

        const selectedPlats = Array.from(document.querySelectorAll('.plat-card.selected'))
                                   .map(c => c.dataset.platId);

        fetch('/add-menu-to-cart', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                menuId: menuId,
                quantity: qty,
                plats: selectedPlats
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                sessionStorage.setItem('menuAdded', true);
                if (typeof updateCartBadge === 'function') {
                    updateCartBadge();
                }
                window.location.href = '/fromages';
            } else {
                alert('Erreur lors de l’ajout au panier.' + (data.message ?? 'erreur inconnue'));
            }
        })
        .catch(() => alert('Erreur lors de l\'ajout.'));
    });

});