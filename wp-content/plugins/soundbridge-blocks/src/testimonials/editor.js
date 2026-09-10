import { InspectorControls, RichText } from '@wordpress/block-editor';
import { Button, PanelBody, SelectControl } from '@wordpress/components';

const StarRating = ({ rating }) => (
    <div className="sb-testimonials__stars" aria-label={`${rating} out of 5 stars`}>
        {Array.from({ length: 5 }, (_, index) => (
            <svg className={index < rating ? 'is-filled' : ''} key={index} viewBox="0 0 14 14" aria-hidden="true">
                <path d="M7 1l1.5 3 3.5.5-2.5 2.5.5 3.5L7 9l-3 1.5.5-3.5L2 4.5l3.5-.5z" />
            </svg>
        ))}
    </div>
);

export default function Edit({ attributes, setAttributes }) {
    const { background = 'white', eyebrow, heading, items = [] } = attributes;
    const sectionClass = `sb-testimonials sb-section alignfull${background !== 'white' ? ` sb-block-bg--${background}` : ''}`;
    const updateItem = (index, key, value) => {
        setAttributes({
            items: items.map((item, itemIndex) => itemIndex === index ? { ...item, [key]: value } : item),
        });
    };
    const moveItem = (index, direction) => {
        const destination = index + direction;
        if (destination < 0 || destination >= items.length) return;

        const nextItems = [...items];
        [nextItems[index], nextItems[destination]] = [nextItems[destination], nextItems[index]];
        setAttributes({ items: nextItems });
    };
    const removeItem = (index) => setAttributes({
        items: items.filter((_, itemIndex) => itemIndex !== index),
    });

    return (
        <>
        <InspectorControls><PanelBody title="Testimonials settings"><SelectControl label="Background color" value={background} options={[{ label: 'White', value: 'white' }, { label: 'Pale blue', value: 'pale-blue' }, { label: 'Dark blue', value: 'dark-blue' }]} onChange={(value) => setAttributes({ background: value })} /></PanelBody></InspectorControls>
        <section className={sectionClass}>
            <div className="sb-container">
                <div className="sb-testimonials__header">
                    <RichText tagName="p" className="sb-eyebrow" value={eyebrow} placeholder="Testimonials" onChange={(value) => setAttributes({ eyebrow: value })} />
                    <RichText tagName="h2" value={heading} placeholder="Section heading" onChange={(value) => setAttributes({ heading: value })} />
                </div>
                <div className="sb-testimonials__grid">
                    {items.map((item, index) => (
                        <article className="sb-testimonial" key={index}>
                            <div className="sb-testimonial__controls">
                                <Button label="Move testimonial left" disabled={index === 0} onClick={() => moveItem(index, -1)} size="small"><span className="dashicons dashicons-arrow-up-alt2" aria-hidden="true" /></Button>
                                <Button label="Move testimonial right" disabled={index === items.length - 1} onClick={() => moveItem(index, 1)} size="small"><span className="dashicons dashicons-arrow-down-alt2" aria-hidden="true" /></Button>
                                <Button label="Remove testimonial" isDestructive onClick={() => removeItem(index)} size="small"><span className="dashicons dashicons-trash" aria-hidden="true" /></Button>
                            </div>
                            <SelectControl className="sb-testimonial__rating-control" label="Rating" value={item.stars ?? 5} options={[1, 2, 3, 4, 5].map((stars) => ({ label: `${stars} star${stars === 1 ? '' : 's'}`, value: stars }))} onChange={(value) => updateItem(index, 'stars', Number(value))} />
                            <StarRating rating={item.stars ?? 5} />
                            <RichText tagName="p" className="sb-testimonial__quote" value={item.body} placeholder="Testimonial quote" onChange={(value) => updateItem(index, 'body', value)} />
                            <div className="sb-testimonial__author">
                                <span className="sb-testimonial__avatar" aria-hidden="true">{item.name?.trim().charAt(0) || '?'}</span>
                                <div>
                                    <RichText tagName="p" className="sb-testimonial__name" value={item.name} allowedFormats={[]} placeholder="Name" onChange={(value) => updateItem(index, 'name', value)} />
                                    <RichText tagName="p" className="sb-testimonial__role" value={item.role} allowedFormats={[]} placeholder="Role" onChange={(value) => updateItem(index, 'role', value)} />
                                </div>
                            </div>
                        </article>
                    ))}
                </div>
                <Button className="sb-testimonials__add" variant="secondary" onClick={() => setAttributes({ items: [...items, { name: '', role: '', body: '', stars: 5 }] })}>
                    <span className="dashicons dashicons-plus" aria-hidden="true" />
                    Add testimonial
                </Button>
            </div>
        </section>
        </>
    );
}
