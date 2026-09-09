import { RichText } from '@wordpress/block-editor';
import { Button } from '@wordpress/components';

export default function Edit({ attributes, setAttributes }) {
    const { eyebrow, heading, items = [] } = attributes;
    const updateItem = (index, key, value) => {
        const nextItems = items.map((item, itemIndex) =>
            itemIndex === index ? { ...item, [key]: value } : item
        );
        setAttributes({ items: nextItems });
    };

    return (
        <section className="sb-section alignfull">
            <div className="sb-container sb-narrow">
                <RichText tagName="p" className="sb-eyebrow" value={eyebrow} placeholder="Eyebrow" onChange={(value) => setAttributes({ eyebrow: value })} />
                <RichText tagName="h2" value={heading} placeholder="Heading" onChange={(value) => setAttributes({ heading: value })} />
                <div className="sb-faq">
                    {items.map((item, index) => (
                        <details open key={index}>
                            <summary><RichText tagName="span" value={item.q} placeholder="Question" onChange={(value) => updateItem(index, 'q', value)} /></summary>
                            <div><RichText tagName="p" value={item.a} placeholder="Answer" onChange={(value) => updateItem(index, 'a', value)} /></div>
                            <Button isDestructive variant="link" onClick={() => setAttributes({ items: items.filter((_, itemIndex) => itemIndex !== index) })}>Remove question</Button>
                        </details>
                    ))}
                </div>
                <Button variant="secondary" onClick={() => setAttributes({ items: [...items, { q: '', a: '' }] })}>Add question</Button>
            </div>
        </section>
    );
}
