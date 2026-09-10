import { RichText } from '@wordpress/block-editor';

export default function save({ attributes }) {
    const { eyebrow, heading, items = [] } = attributes;
    return (
        <section className="sb-timeline sb-section alignfull">
            <div className="sb-container sb-narrow">
                <header className="sb-timeline__header">
                    {eyebrow && <RichText.Content tagName="p" className="sb-eyebrow" value={eyebrow} />}
                    <RichText.Content tagName="h2" value={heading} />
                </header>
                <div className="sb-timeline__list">
                    {items.map((item, index) => (
                        <article className="sb-timeline__item" key={index}>
                            <div className="sb-timeline__marker"><RichText.Content tagName="span" value={item.year || ''} /></div>
                            <div className="sb-timeline__content">
                                <RichText.Content tagName="p" className="sb-timeline__year" value={item.year || ''} />
                                <RichText.Content tagName="h3" value={item.title || ''} />
                                <RichText.Content tagName="p" className="sb-timeline__body" value={item.body || ''} />
                            </div>
                        </article>
                    ))}
                </div>
            </div>
        </section>
    );
}
