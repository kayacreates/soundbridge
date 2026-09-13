import { RichText } from '@wordpress/block-editor';

export default function save({ attributes }) {
    const { background = 'white', layoutStyle = 'homepage', eyebrow, heading, imageUrl, imageAlt, showButton = true, buttonLabel, buttonUrl, items = [] } = attributes;
    const isSimple = layoutStyle === 'simple';
    const sectionClass = `sb-faq sb-block-bg alignfull${background !== 'white' ? ` sb-block-bg--${background}` : ''}`;
    const gridClass = `sb-container sb-faq__grid${isSimple ? ' sb-faq__grid--simple' : imageUrl ? '' : ' sb-faq__grid--no-image'}`;

    return (
        <section id={attributes.anchor || undefined} className={sectionClass}>
            <div className={gridClass}>
                {!isSimple && imageUrl && <div className="sb-faq__media">
                    <img src={imageUrl} alt={imageAlt || ''} loading="lazy" />
                </div>}
                <div className="sb-faq__content">
                    <p className="sb-badge">{eyebrow}</p>
                    <RichText.Content tagName="h2" value={heading} />
                    <div className="sb-faq__list">
                        {items.map((item, index) => (
                            <details key={index}>
                                <summary>
                                    <RichText.Content tagName="span" value={item.q || ''} />
                                    <span className="sb-faq__chevron" aria-hidden="true" />
                                </summary>
                                <div className="sb-faq__answer"><RichText.Content tagName="p" value={item.a || ''} /></div>
                            </details>
                        ))}
                    </div>
                    {showButton && <a className="sb-btn sb-btn--outline sb-faq__button" href={buttonUrl}><RichText.Content tagName="span" value={buttonLabel} /></a>}
                </div>
            </div>
        </section>
    );
}
