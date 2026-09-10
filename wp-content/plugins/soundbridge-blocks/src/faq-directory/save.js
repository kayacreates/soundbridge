import { RichText } from '@wordpress/block-editor';

export default function save({ attributes }) {
    const { background = 'white', categories = [] } = attributes;
    const total = categories.reduce((count, category) => count + category.items.length, 0);
    return <section className={`sb-faq-directory sb-block-bg alignfull sb-block-bg--${background}`} data-sb-faq-directory><div className="sb-container">
        <div className="sb-faq-directory__search"><span aria-hidden="true">⌕</span><input type="search" placeholder="Search all questions…" aria-label="Search frequently asked questions" data-sb-faq-search /></div>
        <div className="sb-faq-directory__layout"><nav className="sb-faq-directory__nav" aria-label="FAQ categories"><strong>Categories</strong><button type="button" className="is-active" data-sb-faq-filter="all">All Topics <span>{total}</span></button>{categories.map((category, index) => <button type="button" data-sb-faq-filter={String(index)} key={index}>{category.icon} {category.label} <span>{category.items.length}</span></button>)}</nav><div className="sb-faq-directory__content"><p className="sb-faq-directory__results" data-sb-faq-results hidden /><div className="sb-faq-directory__empty" data-sb-faq-empty hidden><h3>No results found</h3><p>Try different search terms or browse all categories.</p></div>{categories.map((category, categoryIndex) => <section className="sb-faq-directory__category" data-sb-faq-category={categoryIndex} key={categoryIndex}><h2><span aria-hidden="true">{category.icon}</span> {category.label}</h2>{category.items.map((item, itemIndex) => <details data-sb-faq-item data-search={`${item.q} ${item.a}`.toLowerCase()} key={itemIndex}><summary><RichText.Content tagName="span" value={item.q || ''} /></summary><RichText.Content tagName="p" value={item.a || ''} /></details>)}</section>)}</div></div>
    </div></section>;
}
