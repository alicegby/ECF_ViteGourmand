document.addEventListener('DOMContentLoaded', function() {
    const nbMenusInput = document.querySelector('input[name="nbPersonne"]');
    const totalEl = document.getElementById('totalGeneral');
    const form = document.querySelector('form');

    // ----- Validation minimum options -----
    form.addEventListener('submit', function(e) {
        const errors = [];
        form.querySelectorAll('input[type="number"][data-min]').forEach(input => {
            const min = parseInt(input.dataset.min, 10) || 0;
            const val = parseInt(input.value, 10) || 0;
            if (val > 0 && val < min) errors.push(`${input.closest('label').innerText.trim()} : minimum ${min}`);
        });
        if (errors.length) {
            e.preventDefault();
            alert('Attention :\n' + errors.join('\n'));
        }
    });

    // ----- Calcul total -----
    function calculateTotal() {
        const nbPersonne = parseInt(nbMenusInput.value) || 0;
        const prixParPersonne = parseFloat(nbMenusInput.dataset.prixParPersonne) || 0;
        let optionsTotal = 0;

        document.querySelectorAll('.modify-options input[type="number"]').forEach(input => {
            const prix = parseFloat(input.dataset.prix) || 0;
            const qty = parseFloat(input.value) || 0;
            optionsTotal += prix * qty;
        });

        let total = nbPersonne * prixParPersonne + optionsTotal;

        // remise 10% si nbPersonne > min
        const minPers = parseInt(nbMenusInput.min) || 0;
        if (nbPersonne - minPers >= 5) total -= total * 0.10;

        totalEl.textContent = total.toFixed(2) + ' €';

        // ----- AJAX pour mise à jour côté serveur -----
        const formData = new FormData(form);
        formData.append('ajax', '1');

        fetch(form.action, { method: 'POST', body: formData, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    totalEl.textContent = data.prixTotal + ' €';
                }
            });
    }

    // recalcul automatique à chaque changement
    document.querySelectorAll('.modify-options input[type="number"], input[name="nbPersonne"]')
        .forEach(input => input.addEventListener('input', calculateTotal));

    // calcul au chargement
    calculateTotal();
});