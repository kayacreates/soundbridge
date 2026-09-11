document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('[data-sb-event-archive]').forEach((archive) => {
    const tabs = [...archive.querySelectorAll('[data-sb-event-tab]')];
    const events = [...archive.querySelectorAll('[data-sb-event-item]')];
    const grid = archive.querySelector('[data-sb-event-grid]');
    const empty = archive.querySelector('[data-sb-event-empty]');
    const emptyHeading = archive.querySelector('[data-sb-event-empty-heading]');

    const show = (period) => {
      let visible = 0;
      events.forEach((event) => {
        event.hidden = event.dataset.period !== period;
        if (!event.hidden) visible += 1;
      });
      tabs.forEach((tab) => {
        const active = tab.dataset.sbEventTab === period;
        tab.classList.toggle('is-active', active);
        tab.setAttribute('aria-selected', String(active));
      });
      grid.hidden = visible === 0;
      empty.hidden = visible !== 0;
      emptyHeading.textContent = `No ${period} events right now`;
    };

    tabs.forEach((tab) => tab.addEventListener('click', () => show(tab.dataset.sbEventTab)));
    show('upcoming');
  });
});
