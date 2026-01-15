document.addEventListener('DOMContentLoaded', () => {
    // Bouton retour
    const btnBack = document.querySelector('.btn-back-edit');
    btnBack?.addEventListener('click', () => {
        const url = btnBack.dataset.url;
        if (url) window.location.href = url;
    });
});