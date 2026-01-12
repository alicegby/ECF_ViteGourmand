// main.js – Menu burger responsive + badge panier

document.addEventListener('DOMContentLoaded', () => {

    // -------------------
    // Menu burger
    // -------------------
    const burger = document.querySelector('.burger');
    const mobileMenu = document.querySelector('.mobile-menu');
    const closeBtn = document.querySelector('.close-btn');
    const menuLinks = document.querySelectorAll('.mobile-menu a');

    if (burger && mobileMenu && closeBtn) {
        // Ouvrir le menu
        burger.addEventListener('click', () => {
            mobileMenu.classList.add('active');
        });

        // Fermer le menu quand on clique sur la croix
        closeBtn.addEventListener('click', () => {
            mobileMenu.classList.remove('active');
        });

        // Fermer le menu quand on clique sur un lien
        menuLinks.forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.remove('active');
            });
        });
    }

    // -------------------
    // Badge panier
    // -------------------
    function updateCartBadge() {
        const badge = document.querySelector('.icon-wrapper .cart-badge');
        if (!badge) return;

        fetch('/panier/count', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.json())
            .then(data => {
                if (data.total > 0) {
                    badge.textContent = data.total;
                    badge.style.display = 'flex';
                } else {
                    badge.style.display = 'none';
                }
            })
            .catch(err => console.error('Erreur updateCartBadge:', err));
    }

    // Appel au chargement
    updateCartBadge();
    window.updateCartBadge = updateCartBadge; // permet de réutiliser après un ajout/suppression
});