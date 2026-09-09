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
    const moveItem = (index, direction) => {
        const destinationIndex = index + direction;

        if (destinationIndex < 0 || destinationIndex >= items.length) {
            return;
        }

        const nextItems = [...items];
        [nextItems[index], nextItems[destinationIndex]] = [nextItems[destinationIndex], nextItems[index]];
        setAttributes({ items: nextItems });
    };
    const removeItem = (index) => {
        setAttributes({ items: items.filter((_, itemIndex) => itemIndex !== index) });
    };

    return (
        <section className="sb-stats alignfull">
            <div className="sb-container sb-stats__grid">
                {items.map((item, index) => (
                    <div className="sb-stats__item" key={index}>
                        <div className="sb-stats__controls">
                            <Button className="sb-stats__control" label="Move stat left" disabled={index === 0} onClick={() => moveItem(index, -1)} size="small">
                                <span className="dashicons dashicons-arrow-up-alt2" aria-hidden="true" />
                            </Button>
                            <Button className="sb-stats__control" label="Move stat right" disabled={index === items.length - 1} onClick={() => moveItem(index, 1)} size="small">
                                <span className="dashicons dashicons-arrow-down-alt2" aria-hidden="true" />
                            </Button>
                            <Button className="sb-stats__control" label="Remove stat" isDestructive onClick={() => removeItem(index)} size="small">
                                <span className="dashicons dashicons-trash" aria-hidden="true" />
                            </Button>
                        </div>
                        <RichText tagName="p" className="sb-stats__stat" value={item.value} allowedFormats={[]} placeholder="Value" onChange={(value) => updateItem(index, 'value', value)} />
                        <RichText tagName="p" className="sb-stats__label" value={item.label} allowedFormats={[]} placeholder="Label" onChange={(value) => updateItem(index, 'label', value)} />
                    </div>
                ))}
            </div>
            <Button variant="secondary" onClick={() => setAttributes({ items: [...items, { value: '', label: '' }] })}>
                <span className="dashicons dashicons-plus" aria-hidden="true" />
                Add stat
            </Button>
        </section>
    );
}
