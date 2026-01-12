document.addEventListener('DOMContentLoaded', () => {

    // =====================
    // Récupérations DOM
    // =====================
    const priceRange = document.getElementById('price-range');
    const priceValue = document.getElementById('price-value');
    const personsRange = document.getElementById('persons-range');
    const personsValue = document.getElementById('persons-value');

    const filterToggle = document.querySelector('.filter-toggle');
    const filterPanel = document.querySelector('.filter-panel');
    const filterOverlay = document.querySelector('.filter-overlay');
    const closePanel = document.querySelector('.close-panel');

    const applyBtn = document.querySelector('.apply-filters');
    const resetBtn = document.querySelector('.reset-filters');

    const menus = document.querySelectorAll('.menu-box');
    const keywordInput = document.getElementById('keyword');
    const filterTagsContainer = document.querySelector('.filter-tags');

    let selectedThemes = [];
    let selectedRegimes = [];

    // =====================
    // Affichage sliders
    // =====================
    if (priceRange && priceValue) {
        priceValue.textContent = priceRange.value;
        priceRange.addEventListener('input', () => {
            priceValue.textContent = priceRange.value;
            filterMenus();
        });
    }

    if (personsRange && personsValue) {
        personsValue.textContent = personsRange.value;
        personsRange.addEventListener('input', () => {
            personsValue.textContent = personsRange.value;
            filterMenus();
        });
    }

    // =====================
    // Panneau filtres
    // =====================
    filterToggle?.addEventListener('click', () => {
        filterPanel.classList.add('active');
        filterOverlay.classList.add('active');
    });

    function closeFilterPanel() {
        filterPanel?.classList.remove('active');
        filterOverlay?.classList.remove('active');
    }

    closePanel?.addEventListener('click', closeFilterPanel);
    filterOverlay?.addEventListener('click', closeFilterPanel);

    // =====================
    // Multi-select (themes / regimes)
    // =====================
    function setupMultiSelect(listId, callback) {
        document.querySelectorAll(`#${listId} .filter-option`).forEach(option => {
            option.addEventListener('click', () => {
                option.classList.toggle('selected');
                const values = Array.from(document.querySelectorAll(`#${listId} .filter-option.selected`))
                                    .map(el => el.dataset.id);
                callback(values);
                filterMenus();
            });
        });
    }

    setupMultiSelect('themes-list', v => selectedThemes = v);
    setupMultiSelect('regimes-list', v => selectedRegimes = v);

    // =====================
    // Tags
    // =====================
    function updateTags() {
        filterTagsContainer.innerHTML = '';

        function createTags(selectedArray, listId) {
            selectedArray.forEach(id => {
                const el = document.querySelector(`#${listId} .filter-option[data-id="${id}"]`);
                if (!el) return;
                const tag = document.createElement('div');
                tag.className = 'filter-tag';
                tag.innerHTML = `${el.textContent} <button>×</button>`;
                tag.querySelector('button').addEventListener('click', () => {
                    el.classList.remove('selected');
                    selectedArray.splice(selectedArray.indexOf(id), 1);
                    filterMenus();
                });
                filterTagsContainer.appendChild(tag);
            });
        }

        createTags(selectedThemes, 'themes-list');
        createTags(selectedRegimes, 'regimes-list');
    }

    // =====================
    // Filtrage menus
    // =====================
    function filterMenus() {
        const keyword = (keywordInput?.value || '').toLowerCase();
        const maxPrice = parseFloat(priceRange?.value || Infinity);
        const minPersons = parseInt(personsRange?.value || 0, 10);

        menus.forEach(menu => {
            const price = parseFloat(menu.dataset.price || 0);
            const persons = parseInt(menu.dataset.persons || 0, 10);
            const menuKeyword = (menu.dataset.keyword || '').toLowerCase();

            const matchTheme = selectedThemes.length === 0 || selectedThemes.includes(menu.dataset.theme);
            const matchRegime = selectedRegimes.length === 0 || selectedRegimes.includes(menu.dataset.regime);
            const matchKeyword = !keyword || menuKeyword.includes(keyword);
            const matchPrice = price <= maxPrice;
            const matchPersons = persons >= minPersons;

            menu.style.display = (matchTheme && matchRegime && matchKeyword && matchPrice && matchPersons) ? 'flex' : 'none';
        });

        updateTags();
    }

    // =====================
    // Reset filtres
    // =====================
    resetBtn?.addEventListener('click', () => {
        selectedThemes = [];
        selectedRegimes = [];
        document.querySelectorAll('.filter-option.selected').forEach(el => el.classList.remove('selected'));

        if (keywordInput) keywordInput.value = '';
        if (priceRange) { priceRange.value = priceRange.min; priceValue.textContent = priceRange.value; }
        if (personsRange) { personsRange.value = personsRange.min; personsValue.textContent = personsRange.value; }

        filterMenus();
    });

    keywordInput?.addEventListener('input', filterMenus);

    // =====================
    // Carrousel images
    // =====================
    menus.forEach(menu => {
        const wrapper = menu.querySelector('.gallery-wrapper');
        if (!wrapper) return;

        const slides = wrapper.querySelectorAll('.gallery-slide');
        if (slides.length <= 1) return;

        let index = 0;
        const prevBtn = menu.querySelector('.prev-slide');
        const nextBtn = menu.querySelector('.next-slide');

        function showSlide(i) {
            wrapper.style.transform = `translateX(-${i * 100}%)`;
        }

        prevBtn?.addEventListener('click', () => {
            index = (index - 1 + slides.length) % slides.length;
            showSlide(index);
        });

        nextBtn?.addEventListener('click', () => {
            index = (index + 1) % slides.length;
            showSlide(index);
        });

        setInterval(() => {
            index = (index + 1) % slides.length;
            showSlide(index);
        }, 3000);
    });

});