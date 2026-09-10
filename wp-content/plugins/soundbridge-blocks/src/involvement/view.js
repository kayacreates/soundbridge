document.querySelectorAll('[data-sb-involvement]').forEach((block) => {
    const tabs = [...block.querySelectorAll('[data-sb-pathway-tab]')];
    const panels = [...block.querySelectorAll('[data-sb-pathway-panel]')];
    tabs.forEach((tab) => tab.addEventListener('click', () => {
        const selected = tab.dataset.sbPathwayTab;
        tabs.forEach((item) => {
            const active = item.dataset.sbPathwayTab === selected;
            item.classList.toggle('is-active', active);
            item.setAttribute('aria-selected', String(active));
        });
        panels.forEach((panel) => {
            const active = panel.dataset.sbPathwayPanel === selected;
            panel.classList.toggle('is-active', active);
            panel.hidden = !active;
        });
    }));
});
