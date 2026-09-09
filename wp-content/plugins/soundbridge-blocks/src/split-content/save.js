import { RichText } from '@wordpress/block-editor';

export default function save({ attributes }) {
    const {
        eyebrow,
        heading,
        text,
        imageUrl,
        imageAlt,
        secondaryImageUrl,
        secondaryImageAlt,
        metrics = [],
        showButton,
        buttonLabel,
        buttonUrl,
        reverse,
        background,
    } = attributes;
    const sectionClass = `sb-about sb-section alignfull${background === 'blue' ? ' sb-section--pale' : ''}`;
    const gridClass = `sb-container sb-about__grid${reverse ? ' sb-about__grid--reverse' : ''}`;

    return (
        <section className={sectionClass}>
            <div className={gridClass}>
                <div className="sb-about__media">
                    {imageUrl && <div className="sb-about__image--primary"><img src={imageUrl} alt={imageAlt} loading="lazy" /></div>}
                    {secondaryImageUrl && <div className="sb-about__image--secondary"><img src={secondaryImageUrl} alt={secondaryImageAlt} loading="lazy" /></div>}
                </div>
                <div className="sb-about__content">
                    {eyebrow && <RichText.Content tagName="p" className="sb-about__badge" value={eyebrow} />}
                    <RichText.Content tagName="h2" value={heading} />
                    <RichText.Content tagName="p" className="sb-copy" value={text} />
                    {metrics.length > 0 && (
                        <div className="sb-about__metrics">
                            {metrics.map((metric, index) => (
                                <div className="sb-about__metric" key={index}>
                                    <RichText.Content tagName="strong" value={metric.value || ''} />
                                    <RichText.Content tagName="span" className="sb-about__metric-label" value={metric.label || ''} />
                                    <RichText.Content tagName="small" value={metric.detail || ''} />
                                    <div className="sb-about__progress"><span style={{ width: `${metric.progress || 0}%` }} /></div>
                                </div>
                            ))}
                        </div>
                    )}
                    {showButton && <a className="sb-btn sb-btn--outline sb-about__button" href={buttonUrl}>{buttonLabel}</a>}
                </div>
            </div>
        </section>
    );
}
