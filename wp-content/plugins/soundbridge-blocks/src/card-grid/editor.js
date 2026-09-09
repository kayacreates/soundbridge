import { InspectorControls, RichText } from '@wordpress/block-editor';
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
                                <RichText tagName="h3" value={card.title} placeholder="Card title" onChange={(value) => updateCard(index, 'title', value)} />
                                <RichText tagName="p" value={card.text} placeholder="Card text" onChange={(value) => updateCard(index, 'text', value)} />
                                <Button isDestructive variant="link" onClick={() => setAttributes({ cards: cards.filter((_, cardIndex) => cardIndex !== index) })}>Remove card</Button>
                            </article>
                        ))}
                    </div>
                    <Button variant="secondary" onClick={() => setAttributes({ cards: [...cards, { title: '', text: '' }] })}>Add card</Button>
                </div>
            </section>
        </>
    );
}
