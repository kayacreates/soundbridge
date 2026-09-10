import { RichText } from '@wordpress/block-editor';

export default function save({ attributes }) {
    const {
        heroStyle,
        showBreadcrumbs,
        breadcrumbHomeLabel,
        breadcrumbHomeUrl,
        breadcrumbCurrentLabel,
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
    const isInnerHero = heroStyle === 'inner';
    const heroClass = `sb-hero alignfull sb-hero--${isInnerHero ? 'inner' : (background || 'white')}`;
    const heroCopy = (
        <>
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
        </>
    );

    return (
        <section className={heroClass}>
            {showBreadcrumbs && (
                <div className="sb-hero__breadcrumb-bar">
                    <nav className="sb-container sb-hero__breadcrumbs" aria-label="Breadcrumb">
                        <a href={breadcrumbHomeUrl}>{breadcrumbHomeLabel}</a>
                        <span className="sb-hero__breadcrumb-separator" aria-hidden="true">›</span>
                        <span aria-current="page">{breadcrumbCurrentLabel}</span>
                    </nav>
                </div>
            )}
            {isInnerHero ? (
                <div className="sb-hero__inner">
                    {imageUrl && <img className="sb-hero__inner-image" src={imageUrl} alt={imageAlt} loading="eager" />}
                    <span className="sb-hero__inner-overlay" aria-hidden="true" />
                    <div className="sb-container sb-hero__inner-content">{heroCopy}</div>
                </div>
            ) : (
                <div className="sb-container sb-hero__grid">
                    <div>{heroCopy}</div>
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
            )}
        </section>
    );
}
