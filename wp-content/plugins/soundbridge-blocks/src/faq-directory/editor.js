import { InspectorControls, RichText, useBlockProps } from '@wordpress/block-editor';
import { Button, PanelBody, SelectControl, TextControl } from '@wordpress/components';

export default function Edit({ attributes, setAttributes }) {
    const { background = 'white', categories = [] } = attributes;
    const updateCategory = (index, values) => setAttributes({ categories: categories.map((category, categoryIndex) => categoryIndex === index ? { ...category, ...values } : category) });
    const updateItem = (categoryIndex, itemIndex, values) => updateCategory(categoryIndex, { items: categories[categoryIndex].items.map((item, index) => index === itemIndex ? { ...item, ...values } : item) });
    return <>
        <InspectorControls><PanelBody title="FAQ Directory settings"><SelectControl label="Background color" value={background} options={[{ label: 'White', value: 'white' }, { label: 'Pale blue', value: 'pale-blue' }, { label: 'Dark blue', value: 'dark-blue' }]} onChange={(value) => setAttributes({ background: value })} /></PanelBody></InspectorControls>
        <section {...useBlockProps({ className: `sb-faq-directory sb-block-bg alignfull sb-block-bg--${background}` })}><div className="sb-container">
            <div className="sb-faq-directory__search">⌕ <span>Search all questions…</span></div>
            <div className="sb-faq-directory__layout"><aside className="sb-faq-directory__nav"><strong>Categories</strong>{categories.map((category, index) => <span key={index}>{category.icon} {category.label}</span>)}</aside><div className="sb-faq-directory__content">
                {categories.map((category, categoryIndex) => <section className="sb-faq-directory__category" key={categoryIndex}><div className="sb-faq-directory__category-editor"><TextControl label="Icon or emoji" value={category.icon} onChange={(value) => updateCategory(categoryIndex, { icon: value })} /><RichText tagName="h2" value={category.label} placeholder="Category" onChange={(value) => updateCategory(categoryIndex, { label: value })} /></div>{category.items.map((item, itemIndex) => <details open key={itemIndex}><summary><RichText tagName="span" value={item.q} placeholder="Question" onChange={(value) => updateItem(categoryIndex, itemIndex, { q: value })} /></summary><RichText tagName="p" value={item.a} placeholder="Answer" onChange={(value) => updateItem(categoryIndex, itemIndex, { a: value })} /><Button isDestructive variant="link" onClick={() => updateCategory(categoryIndex, { items: category.items.filter((_, index) => index !== itemIndex) })}>Remove question</Button></details>)}<Button variant="secondary" onClick={() => updateCategory(categoryIndex, { items: [...category.items, { q: '', a: '' }] })}>Add question</Button><Button isDestructive variant="link" onClick={() => setAttributes({ categories: categories.filter((_, index) => index !== categoryIndex) })}>Remove category</Button></section>)}
                <Button variant="secondary" onClick={() => setAttributes({ categories: [...categories, { icon: '❓', label: '', items: [] }] })}>Add category</Button>
            </div></div>
        </div></section>
    </>;
}
