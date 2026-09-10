import { InspectorControls, MediaUpload, MediaUploadCheck, RichText } from '@wordpress/block-editor';
import { PanelBody, SelectControl, Button } from '@wordpress/components';

export default function Edit({ attributes, setAttributes }) {
    const { background = 'white', mediaStyle = 'full', columns = 3, textAlignment, eyebrow, heading, intro, cards = [] } = attributes;
    const contentClass = `sb-card-content has-text-align-${textAlignment || 'left'}`;
    const sectionClass = `sb-section alignfull${background !== 'white' ? ` sb-block-bg--${background}` : ''}`;
    const gridClass = `sb-card-grid${columns !== 3 ? ` sb-card-grid--${columns}` : ''}`;
    const iconClass = `sb-card__icon${mediaStyle === 'small' ? ' sb-card__icon--small' : ''}`;
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
                    <SelectControl label="Background color" value={background} options={[{ label: 'White', value: 'white' }, { label: 'Pale blue', value: 'pale-blue' }, { label: 'Dark blue', value: 'dark-blue' }]} onChange={(value) => setAttributes({ background: value })} />
                    <SelectControl label="SVG display" value={mediaStyle} options={[{ label: 'Full-size image', value: 'full' }, { label: 'Small icon', value: 'small' }, { label: 'None', value: 'none' }]} onChange={(value) => setAttributes({ mediaStyle: value })} />
                    <SelectControl label="Columns" value={String(columns)} options={[{ label: '2 columns', value: '2' }, { label: '3 columns', value: '3' }, { label: '4 columns', value: '4' }]} onChange={(value) => setAttributes({ columns: Number(value) })} />
                    <SelectControl label="Text Alignment" value={textAlignment} options={[{ label: 'Left', value: 'left' }, { label: 'Center', value: 'center' }, { label: 'Right', value: 'right' }]} onChange={(value) => setAttributes({ textAlignment: value })} />
                </PanelBody>
            </InspectorControls>
            <section className={sectionClass}>
                <div className="sb-container">
                    <div className={contentClass}>
                        <RichText tagName="p" className="sb-eyebrow" value={eyebrow} placeholder="Eyebrow" onChange={(value) => setAttributes({ eyebrow: value })} />
                        <RichText tagName="h2" value={heading} placeholder="Heading" onChange={(value) => setAttributes({ heading: value })} />
                        <RichText tagName="p" className="sb-lead" value={intro} placeholder="Introduction" onChange={(value) => setAttributes({ intro: value })} />
                    </div>
                    <div className={gridClass}>
                        {cards.map((card, index) => (
                            <article className="sb-card" key={index}>
                                {mediaStyle !== 'none' && <div className={iconClass}>
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
                                            <MediaUpload
                                                allowedTypes={['image/svg+xml']}
                                                value={card.iconId || 0}
                                                onSelect={(media) => selectIcon(index, media)}
                                                render={({ open }) => mediaStyle === 'small' ? (
                                                    <Button className="sb-card__choose-icon" label="Choose SVG icon" onClick={open}>
                                                        <span className="dashicons dashicons-format-image" aria-hidden="true" />
                                                    </Button>
                                                ) : (
                                                    <Button variant="secondary" size="small" onClick={open}>Choose SVG icon</Button>
                                                )}
                                            />
                                        </MediaUploadCheck>
                                    )}
                                </div>}
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
