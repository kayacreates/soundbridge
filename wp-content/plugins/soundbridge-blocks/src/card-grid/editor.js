import { RichText } from '@wordpress/block-editor';
import { Button } from '@wordpress/components';

export default function Edit({ attributes, setAttributes }) {
    const { eyebrow, heading, intro, cards = [] } = attributes;
    const updateCard = (index, key, value) => {
        const nextCards = cards.map((card, cardIndex) =>
            cardIndex === index ? { ...card, [key]: value } : card
        );
        setAttributes({ cards: nextCards });
    };

    return (
        <section className="sb-section alignfull">
            <div className="sb-container">
                <RichText tagName="p" className="sb-eyebrow" value={eyebrow} placeholder="Eyebrow" onChange={(value) => setAttributes({ eyebrow: value })} />
                <RichText tagName="h2" value={heading} placeholder="Heading" onChange={(value) => setAttributes({ heading: value })} />
                <RichText tagName="p" className="sb-lead" value={intro} placeholder="Introduction" onChange={(value) => setAttributes({ intro: value })} />
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
    );
}
