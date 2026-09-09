import { RichText } from '@wordpress/block-editor';

export default function save({ attributes }) {
    const {
        background,
        eyebrow,
        heading,
        text,
        showStats,
        stats = [],
        imageUrl,
        imageAlt,
        showRegistration,
        registrationStatus,
        registrationTitle,
        registrationDetails,
        registrationButtonLabel,
        registrationButtonUrl,
        showPrimaryButton,
        primaryLabel,
        primaryUrl,
        showSecondaryButton,
        secondaryLabel,
        secondaryUrl,
    } = attributes;
    const heroClass = `sb-hero alignfull sb-hero--${background || 'white'}`;

    return (
        <section className={heroClass}>
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
                    {showStats && stats.length > 0 && (
                        <div className="sb-hero__stats">
                            {stats.map((stat, index) => (
                                <div className="sb-hero__stat" key={index}>
                                    <RichText.Content tagName="strong" value={stat.value || ''} />
                                    <RichText.Content tagName="span" value={stat.label || ''} />
                                </div>
                            ))}
                        </div>
                    )}
                </div>
                {(imageUrl || showRegistration) && (
                    <div className="sb-hero__media">
                        {imageUrl && <img className="sb-hero__image" src={imageUrl} alt={imageAlt} />}
                        {showRegistration && (
                            <div className="sb-hero__registration">
                                <RichText.Content tagName="span" className="sb-hero__registration-status" value={registrationStatus} />
                                <RichText.Content tagName="strong" className="sb-hero__registration-title" value={registrationTitle} />
                                <RichText.Content tagName="span" className="sb-hero__registration-details" value={registrationDetails} />
                                <a className="sb-hero__registration-button" href={registrationButtonUrl}>{registrationButtonLabel}</a>
                            </div>
                        )}
                    </div>
                )}
            </div>
        </section>
    );
}
