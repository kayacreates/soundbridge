import { InspectorControls, MediaUpload, MediaUploadCheck, RichText } from '@wordpress/block-editor';
import { PanelBody, SelectControl, Button } from '@wordpress/components';

export default function Edit({ attributes, setAttributes }) {
    const { textAlignment, eyebrow, heading, intro, cards = [] } = attributes;
    const contentClass = `sb-card-content has-text-align-${textAlignment || 'left'}`;
    const updateCard = (index, key, value) => {
        const nextCards = cards.map((card, cardIndex) =>
            cardIndex === index ? { ...card, [key]: value } : card
        );
        setAttributes({ cards: nextCards });
    };
    const selectIcon = (index, media) => {
        const nextCards = cards.map((card, cardIndex) =>
            cardIndex === index ? {
                ...card,
                iconId: media.id,
                iconUrl: media.url,
                iconAlt: media.alt || media.title || '',
            } : card
        );
        setAttributes({ cards: nextCards });
    };
    const removeIcon = (index) => {
        const nextCards = cards.map((card, cardIndex) =>
            cardIndex === index ? { ...card, iconId: 0, iconUrl: '', iconAlt: '' } : card
        );
        setAttributes({ cards: nextCards });
    };

    return (
        <>
            <InspectorControls>
                <PanelBody title="Card Grid settings">
                    <SelectControl label="Text Alignment" value={textAlignment} options={[{ label: 'Left', value: 'left' }, { label: 'Center', value: 'center' }, { label: 'Right', value: 'right' }]} onChange={(value) => setAttributes({ textAlignment: value })} />
                </PanelBody>
            </InspectorControls>
            <section className="sb-section alignfull">
                <div className="sb-container">
                    <div className={contentClass}>
                        <RichText tagName="p" className="sb-eyebrow" value={eyebrow} placeholder="Eyebrow" onChange={(value) => setAttributes({ eyebrow: value })} />
                        <RichText tagName="h2" value={heading} placeholder="Heading" onChange={(value) => setAttributes({ heading: value })} />
                        <RichText tagName="p" className="sb-lead" value={intro} placeholder="Introduction" onChange={(value) => setAttributes({ intro: value })} />
                    </div>
                    <div className="sb-card-grid">
                        {cards.map((card, index) => (
                            <article className="sb-card" key={index}>
                                <div className="sb-card__icon">
                                    {card.iconUrl ? (
                                        <>
                                            <span
                                                className="sb-card__svg"
                                                role={card.iconAlt ? 'img' : undefined}
                                                aria-label={card.iconAlt || undefined}
                                                aria-hidden={card.iconAlt ? undefined : true}
                                                style={{ '--sb-card-svg': `url("${card.iconUrl}")` }}
                                            />
                                            <div className="sb-card__icon-actions">
                                                <MediaUploadCheck>
                                                    <MediaUpload allowedTypes={['image/svg+xml']} value={card.iconId || 0} onSelect={(media) => selectIcon(index, media)} render={({ open }) => <Button variant="primary" size="small" onClick={open}>Replace SVG</Button>} />
                                                </MediaUploadCheck>
                                                <Button variant="secondary" size="small" isDestructive onClick={() => removeIcon(index)}>Remove</Button>
                                            </div>
                                        </>
                                    ) : (
                                        <MediaUploadCheck>
                                            <MediaUpload allowedTypes={['image/svg+xml']} value={card.iconId || 0} onSelect={(media) => selectIcon(index, media)} render={({ open }) => <Button variant="secondary" size="small" onClick={open}>Choose SVG icon</Button>} />
                                        </MediaUploadCheck>
                                    )}
                                </div>
                                <RichText tagName="h3" value={card.title} placeholder="Card title" onChange={(value) => updateCard(index, 'title', value)} />
                                <RichText tagName="p" value={card.text} placeholder="Card text" onChange={(value) => updateCard(index, 'text', value)} />
                                <Button isDestructive variant="link" onClick={() => setAttributes({ cards: cards.filter((_, cardIndex) => cardIndex !== index) })}>Remove card</Button>
                            </article>
                        ))}
                    </div>
                    <Button variant="secondary" onClick={() => setAttributes({ cards: [...cards, { iconId: 0, iconUrl: '', iconAlt: '', title: '', text: '' }] })}>Add card</Button>
                </div>
            </section>
        </>
    );
}
