import { RichText } from '@wordpress/block-editor';
import { Button } from '@wordpress/components';

export default function Edit({ attributes, setAttributes }) {
    const { items = [] } = attributes;
    const updateItem = (index, key, value) => {
        const nextItems = items.map((item, itemIndex) =>
            itemIndex === index ? { ...item, [key]: value } : item
        );
        setAttributes({ items: nextItems });
    };

    return (
        <section className="sb-stats alignfull">
            <div className="sb-container sb-stats__grid">
                {items.map((item, index) => (
                    <div key={index}>
                        <RichText tagName="strong" value={item.value} placeholder="Value" onChange={(value) => updateItem(index, 'value', value)} />
                        <RichText tagName="span" value={item.label} placeholder="Label" onChange={(value) => updateItem(index, 'label', value)} />
                        <Button isDestructive variant="link" onClick={() => setAttributes({ items: items.filter((_, itemIndex) => itemIndex !== index) })}>Remove stat</Button>
                    </div>
                ))}
            </div>
            <Button variant="secondary" onClick={() => setAttributes({ items: [...items, { value: '', label: '' }] })}>Add stat</Button>
        </section>
    );
}
