import { RichText } from '@wordpress/block-editor';

export default function save({ attributes }) {
    const {
        eyebrow,
        heading,
        text,
        imageUrl,
        imageAlt,
        showPrimaryButton,
        primaryLabel,
        primaryUrl,
        showSecondaryButton,
        secondaryLabel,
        secondaryUrl,
    } = attributes;

    return (
        <section className="sb-hero alignfull">
            <div className="sb-container sb-hero__grid">
                <div>
                    <p className="sb-eyebrow">{eyebrow}</p>
                    <RichText.Content tagName="h1" value={heading} />
                    <RichText.Content tagName="p" className="sb-lead" value={text} />
                    {(showPrimaryButton || showSecondaryButton) && (
                        <div className="sb-actions">
                            {showPrimaryButton && <a className="sb-btn" href={primaryUrl}>{primaryLabel}</a>}
                            {showSecondaryButton && <a className="sb-btn sb-btn--outline" href={secondaryUrl}>{secondaryLabel}</a>}
                        </div>
                    )}
                </div>
                {imageUrl && <img className="sb-hero__image" src={imageUrl} alt={imageAlt} />}
            </div>
        </section>
    );
}
