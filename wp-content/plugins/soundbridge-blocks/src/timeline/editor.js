import { RichText } from '@wordpress/block-editor';
import { Button } from '@wordpress/components';

export default function Edit({ attributes, setAttributes }) {
    const { eyebrow, heading, items = [] } = attributes;
    const updateItem = (index, key, value) => setAttributes({ items: items.map((item, itemIndex) => itemIndex === index ? { ...item, [key]: value } : item) });
    const moveItem = (index, direction) => {
        const destination = index + direction;
        if (destination < 0 || destination >= items.length) return;
        const nextItems = [...items];
        [nextItems[index], nextItems[destination]] = [nextItems[destination], nextItems[index]];
        setAttributes({ items: nextItems });
    };

    return (
        <section className="sb-timeline sb-section alignfull">
            <div className="sb-container sb-narrow">
                <header className="sb-timeline__header">
                    <RichText tagName="p" className="sb-eyebrow" value={eyebrow} placeholder="Eyebrow" onChange={(value) => setAttributes({ eyebrow: value })} />
                    <RichText tagName="h2" value={heading} placeholder="Heading" onChange={(value) => setAttributes({ heading: value })} />
                </header>
                <div className="sb-timeline__list">
                    {items.map((item, index) => (
                        <article className="sb-timeline__item" key={index}>
                            <div className="sb-timeline__marker"><RichText tagName="span" value={item.year} allowedFormats={[]} placeholder="Year" onChange={(value) => updateItem(index, 'year', value)} /></div>
                            <div className="sb-timeline__content">
                                <div className="sb-timeline__controls">
                                    <Button label="Move milestone up" disabled={index === 0} onClick={() => moveItem(index, -1)} size="small"><span className="dashicons dashicons-arrow-up-alt2" aria-hidden="true" /></Button>
                                    <Button label="Move milestone down" disabled={index === items.length - 1} onClick={() => moveItem(index, 1)} size="small"><span className="dashicons dashicons-arrow-down-alt2" aria-hidden="true" /></Button>
                                    <Button label="Remove milestone" isDestructive onClick={() => setAttributes({ items: items.filter((_, itemIndex) => itemIndex !== index) })} size="small"><span className="dashicons dashicons-trash" aria-hidden="true" /></Button>
                                </div>
                                <RichText tagName="p" className="sb-timeline__year" value={item.year} allowedFormats={[]} placeholder="Year" onChange={(value) => updateItem(index, 'year', value)} />
                                <RichText tagName="h3" value={item.title} placeholder="Milestone title" onChange={(value) => updateItem(index, 'title', value)} />
                                <RichText tagName="p" className="sb-timeline__body" value={item.body} placeholder="Milestone description" onChange={(value) => updateItem(index, 'body', value)} />
                            </div>
                        </article>
                    ))}
                </div>
                <Button variant="secondary" onClick={() => setAttributes({ items: [...items, { year: '', title: '', body: '' }] })}><span className="dashicons dashicons-plus" aria-hidden="true" />Add milestone</Button>
            </div>
        </section>
    );
}
