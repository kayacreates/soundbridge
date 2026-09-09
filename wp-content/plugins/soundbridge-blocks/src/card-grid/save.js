import { RichText } from '@wordpress/block-editor';

export default function save({ attributes }) {
    const { textAlignment, eyebrow, heading, intro, cards = [] } = attributes;
    const contentClass = `sb-card-content has-text-align-${textAlignment || 'left'}`;

    return (
        <section className="sb-section alignfull">
            <div className="sb-container">
                <div className={contentClass}>
                    {eyebrow && <p className="sb-eyebrow">{eyebrow}</p>}
                    <RichText.Content tagName="h2" value={heading} />
                    {intro && <p className="sb-lead">{intro}</p>}
                </div>
                <div className="sb-card-grid">
                    {cards.map((card, index) => (
                        <article className="sb-card" key={index}>
                            <RichText.Content tagName="h3" value={card.title || ''} />
                            <RichText.Content tagName="p" value={card.text || ''} />
                        </article>
                    ))}
                </div>
            </div>
        </section>
    );
}
