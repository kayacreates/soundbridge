document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('[data-sb-directory]').forEach((directory) => {
    const search = directory.querySelector('[data-sb-directory-search]');
    const filters = [...directory.querySelectorAll('[data-sb-directory-category]')];
    const items = [...directory.querySelectorAll('[data-sb-directory-item]')];
    const count = directory.querySelector('[data-sb-directory-count]');
    const grid = directory.querySelector('[data-sb-directory-grid]');
    const empty = directory.querySelector('[data-sb-directory-empty]');
    const clear = directory.querySelector('[data-sb-directory-clear]');
    const emptyClear = directory.querySelector('[data-sb-directory-empty-clear]');
    let category = 'all';

    const update = () => {
      const query = search.value.trim().toLocaleLowerCase();
      let visible = 0;
      items.forEach((item) => {
        const matchesText = !query || item.dataset.search.includes(query);
        const matchesCategory = category === 'all' || item.dataset.category === category;
        item.hidden = !(matchesText && matchesCategory);
        if (!item.hidden) visible += 1;
      });
      count.textContent = `${visible} listing${visible === 1 ? '' : 's'} found`;
      grid.hidden = visible === 0;
      empty.hidden = visible !== 0;
      clear.hidden = !query && category === 'all';
    };

    const reset = () => {
      search.value = '';
      category = 'all';
      filters.forEach((filter) => filter.classList.toggle('is-active', filter.dataset.sbDirectoryCategory === 'all'));
      update();
      search.focus();
    };

    search.addEventListener('input', update);
    filters.forEach((filter) => filter.addEventListener('click', () => {
      category = filter.dataset.sbDirectoryCategory;
      filters.forEach((button) => button.classList.toggle('is-active', button === filter));
      update();
    }));
    clear.addEventListener('click', reset);
    emptyClear.addEventListener('click', reset);
    update();
  });
});
