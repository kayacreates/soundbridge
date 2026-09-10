document.querySelectorAll('[data-sb-faq-directory]').forEach((block) => {
    const input = block.querySelector('[data-sb-faq-search]');
    const filters = [...block.querySelectorAll('[data-sb-faq-filter]')];
    const categories = [...block.querySelectorAll('[data-sb-faq-category]')];
    const results = block.querySelector('[data-sb-faq-results]');
    const empty = block.querySelector('[data-sb-faq-empty]');
    let active = 'all';
    const update = () => {
        const query = input.value.trim().toLowerCase();
        let count = 0;
        categories.forEach((category) => {
            let visibleInCategory = 0;
            category.querySelectorAll('[data-sb-faq-item]').forEach((item) => {
                const visible = (active === 'all' || category.dataset.sbFaqCategory === active) && (!query || item.dataset.search.includes(query));
                item.hidden = !visible;
                if (visible) visibleInCategory += 1;
            });
            category.hidden = visibleInCategory === 0;
            count += visibleInCategory;
        });
        results.hidden = !query;
        results.textContent = query ? `${count} result${count === 1 ? '' : 's'} for “${input.value.trim()}”` : '';
        empty.hidden = count !== 0;
    };
    input.addEventListener('input', update);
    filters.forEach((filter) => filter.addEventListener('click', () => {
        active = filter.dataset.sbFaqFilter;
        filters.forEach((item) => item.classList.toggle('is-active', item === filter));
        update();
    }));
});
