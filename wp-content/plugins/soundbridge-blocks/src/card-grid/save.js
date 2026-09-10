import { RichText } from '@wordpress/block-editor';

export default function save({ attributes }) {
    const { background = 'white', mediaStyle = 'full', columns = 3, textAlignment, eyebrow, heading, intro, cards = [] } = attributes;
    const contentClass = `sb-card-content has-text-align-${textAlignment || 'left'}`;
    const sectionClass = `sb-section alignfull${background !== 'white' ? ` sb-card-section--${background}` : ''}`;
    const gridClass = `sb-card-grid${columns !== 3 ? ` sb-card-grid--${columns}` : ''}`;
    const iconClass = `sb-card__icon${mediaStyle === 'small' ? ' sb-card__icon--small' : ''}`;

    return (
        <section className={sectionClass}>
            <div className="sb-container">
                <div className={contentClass}>
                    {eyebrow && <p className="sb-eyebrow">{eyebrow}</p>}
                    <RichText.Content tagName="h2" value={heading} />
                    {intro && <p className="sb-lead">{intro}</p>}
                </div>
                <div className={gridClass}>
                    {cards.map((card, index) => (
                        <article className="sb-card" key={index}>
                            {mediaStyle !== 'none' && card.iconUrl && (
                                <div className={iconClass}>
                                    <span
                                        className="sb-card__svg"
                                        role={card.iconAlt ? 'img' : undefined}
                                        aria-label={card.iconAlt || undefined}
                                        aria-hidden={card.iconAlt ? undefined : true}
                                        style={{ '--sb-card-svg': `url("${card.iconUrl}")` }}
                                    />
                                </div>
                            )}
                            <RichText.Content tagName="h3" value={card.title || ''} />
                            <RichText.Content tagName="p" value={card.text || ''} />
                        </article>
                    ))}
                </div>
            </div>
        </section>
    );
}
