(() => {
  const form = document.querySelector('.sb-program-filters__form');
  let results = document.querySelector('.sb-program-archive__results');
  if (!form || !results) return;

  let controller;
  const clearFilters = form.querySelector('.sb-program-filters__clear');

  const syncClearFilter = (url) => {
    if (!clearFilters) return;
    clearFilters.hidden = !['age', 'level', 'instrument', 'type'].some((key) => url.searchParams.has(key));
  };

  const updateResults = async (url, updateHistory = true) => {
    if (controller) controller.abort();
    const requestController = new AbortController();
    controller = requestController;
    form.classList.add('is-loading');
    results.classList.add('is-loading');
    form.setAttribute('aria-busy', 'true');

    try {
      const response = await fetch(url, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        signal: requestController.signal,
      });
      if (!response.ok) throw new Error(`Program filtering failed: ${response.status}`);

      const documentFragment = new DOMParser().parseFromString(await response.text(), 'text/html');
      const nextResults = documentFragment.querySelector('.sb-program-archive__results');
      if (!nextResults) throw new Error('Program results were missing from the response.');

      results.replaceWith(nextResults);
      results = nextResults;
      if (updateHistory) window.history.pushState({}, '', url);
    } catch (error) {
      if (error.name !== 'AbortError') window.location.assign(url);
    } finally {
      if (controller === requestController) {
        form.classList.remove('is-loading');
        results.classList.remove('is-loading');
        form.removeAttribute('aria-busy');
      }
    }
  };

  form.addEventListener('submit', (event) => {
    event.preventDefault();
    const url = new URL(form.action, window.location.href);
    const formData = new FormData(form);
    formData.forEach((value, key) => {
      if (value) url.searchParams.set(key, value);
    });
    syncClearFilter(url);
    updateResults(url.toString());
  });

  clearFilters?.addEventListener('click', (event) => {
    event.preventDefault();
    form.querySelectorAll('select[name]').forEach((select) => {
      select.value = '';
    });
    const url = new URL(clearFilters.href, window.location.href);
    syncClearFilter(url);
    updateResults(url.toString());
  });

  document.addEventListener('click', (event) => {
    const paginationLink = event.target.closest('.sb-program-archive__results .page-numbers a');
    if (!paginationLink) return;
    event.preventDefault();
    updateResults(paginationLink.href);
  });

  window.addEventListener('popstate', () => {
    const url = new URL(window.location.href);
    form.querySelectorAll('select[name]').forEach((select) => {
      select.value = url.searchParams.get(select.name) || '';
    });
    syncClearFilter(url);
    updateResults(url.toString(), false);
  });
})();
