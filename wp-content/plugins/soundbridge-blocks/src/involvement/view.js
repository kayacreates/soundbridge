const getHash = () => {
    try {
        return decodeURIComponent(window.location.hash.slice(1)).toLowerCase();
    } catch {
        return window.location.hash.slice(1).toLowerCase();
    }
};

const slugify = (value, fallback) => {
    const slug = value
        .normalize('NFKD')
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');
    return slug || fallback;
};

document.querySelectorAll('[data-sb-involvement]').forEach((block, blockIndex) => {
    const tabs = [...block.querySelectorAll('[data-sb-pathway-tab]')];
    const panels = [...block.querySelectorAll('[data-sb-pathway-panel]')];
    const usedAnchors = new Set();

    tabs.forEach((tab, index) => {
        const baseAnchor = slugify(tab.textContent, `pathway-${index + 1}`);
        let anchor = baseAnchor;
        let suffix = 2;
        while (usedAnchors.has(anchor) || document.querySelector(`[id="${anchor}"]`)) {
            anchor = `${baseAnchor}-${suffix++}`;
        }
        usedAnchors.add(anchor);

        const panel = panels.find((item) => item.dataset.sbPathwayPanel === tab.dataset.sbPathwayTab);
        if (!panel) return;
        panel.id = anchor;
        panel.dataset.sbPathwayHash = anchor;
        tab.setAttribute('aria-controls', anchor);
        tab.id = `sb-pathway-tab-${blockIndex}-${index}`;
        tab.setAttribute('tabindex', index === 0 ? '0' : '-1');
        panel.setAttribute('aria-labelledby', tab.id);
    });

    const activate = (selectedTab, updateHash = false) => {
        if (!selectedTab) return;
        const selected = selectedTab.dataset.sbPathwayTab;
        const selectedPanel = panels.find((panel) => panel.dataset.sbPathwayPanel === selected);

        tabs.forEach((tab) => {
            const active = tab === selectedTab;
            tab.classList.toggle('is-active', active);
            tab.setAttribute('aria-selected', String(active));
            tab.setAttribute('tabindex', active ? '0' : '-1');
        });
        panels.forEach((panel) => {
            const active = panel === selectedPanel;
            panel.classList.toggle('is-active', active);
            panel.hidden = !active;
        });

        if (updateHash && selectedPanel) {
            window.history.pushState(null, '', `#${selectedPanel.dataset.sbPathwayHash}`);
        }
    };

    tabs.forEach((tab, index) => {
        tab.addEventListener('click', () => activate(tab, true));
        tab.addEventListener('keydown', (event) => {
            if (!['ArrowLeft', 'ArrowRight', 'Home', 'End'].includes(event.key)) return;
            event.preventDefault();
            let nextIndex = index;
            if (event.key === 'ArrowLeft') nextIndex = (index - 1 + tabs.length) % tabs.length;
            if (event.key === 'ArrowRight') nextIndex = (index + 1) % tabs.length;
            if (event.key === 'Home') nextIndex = 0;
            if (event.key === 'End') nextIndex = tabs.length - 1;
            activate(tabs[nextIndex], true);
            tabs[nextIndex].focus();
        });
    });

    const activateFromHash = (scroll = false) => {
        const hash = getHash();
        const panel = panels.find((item) => item.dataset.sbPathwayHash === hash);
        if (!panel) return false;
        const tab = tabs.find((item) => item.dataset.sbPathwayTab === panel.dataset.sbPathwayPanel);
        activate(tab);
        if (scroll) requestAnimationFrame(() => panel.scrollIntoView({ block: 'start' }));
        return true;
    };

    if (!activateFromHash(true)) activate(tabs[0]);
    window.addEventListener('hashchange', () => activateFromHash());
});
