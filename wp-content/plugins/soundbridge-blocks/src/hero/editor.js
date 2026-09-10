import { InspectorControls, MediaUpload, MediaUploadCheck, RichText } from '@wordpress/block-editor';
import { Button, PanelBody, SelectControl, TextControl, ToggleControl } from '@wordpress/components';

export default function Edit({ attributes, setAttributes }) {
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
        imageId,
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
    const selectImage = (media) => setAttributes({
        imageId: media.id,
        imageUrl: media.url,
        imageAlt: media.alt || media.title || '',
    });
    const removeImage = () => setAttributes({ imageId: 0, imageUrl: '', imageAlt: '' });
    const updateStat = (index, key, value) => {
        const nextStats = stats.map((stat, statIndex) =>
            statIndex === index ? { ...stat, [key]: value } : stat
        );
        setAttributes({ stats: nextStats });
    };
    const heroCopy = (
        <>
            <RichText tagName="p" className="sb-eyebrow" value={eyebrow} placeholder="Eyebrow" onChange={(value) => setAttributes({ eyebrow: value })} />
            <RichText tagName="h1" value={heading} placeholder="Hero heading" onChange={(value) => setAttributes({ heading: value })} />
            <RichText tagName="p" className="sb-lead" value={text} placeholder="Hero text" onChange={(value) => setAttributes({ text: value })} />
            {(showPrimaryButton || showSecondaryButton) && (
                <div className="sb-actions">
                    {showPrimaryButton && <RichText tagName="span" className="sb-btn" value={primaryLabel} placeholder="Primary label" onChange={(value) => setAttributes({ primaryLabel: value })} />}
                    {showSecondaryButton && <RichText tagName="span" className="sb-btn sb-btn--outline" value={secondaryLabel} placeholder="Secondary label" onChange={(value) => setAttributes({ secondaryLabel: value })} />}
                </div>
            )}
            {showStats && (
                <div className="sb-hero__stats">
                    {stats.map((stat, index) => (
                        <div className="sb-hero__stat" key={index}>
                            <RichText tagName="strong" value={stat.value} placeholder="Value" onChange={(value) => updateStat(index, 'value', value)} />
                            <RichText tagName="span" value={stat.label} placeholder="Label" onChange={(value) => updateStat(index, 'label', value)} />
                            <Button isDestructive variant="link" onClick={() => setAttributes({ stats: stats.filter((_, statIndex) => statIndex !== index) })}>Remove</Button>
                        </div>
                    ))}
                    <Button variant="secondary" onClick={() => setAttributes({ stats: [...stats, { value: '', label: '' }] })}>Add stat</Button>
                </div>
            )}
        </>
    );
    const imageControls = imageUrl ? (
        <div className="sb-editor-media__actions">
            <MediaUploadCheck>
                <MediaUpload allowedTypes={['image']} value={imageId} onSelect={selectImage} render={({ open }) => <Button variant="primary" onClick={open}>Replace image</Button>} />
            </MediaUploadCheck>
            <Button variant="secondary" isDestructive onClick={removeImage}>Remove image</Button>
        </div>
    ) : (
        <MediaUploadCheck>
            <MediaUpload allowedTypes={['image']} value={imageId} onSelect={selectImage} render={({ open }) => <Button variant="secondary" onClick={open}>Choose hero image</Button>} />
        </MediaUploadCheck>
    );

    return (
        <>
            <InspectorControls>
                <PanelBody title="Hero settings">
                    <SelectControl label="Hero style" value={heroStyle || 'inner'} options={[{ label: 'Home page', value: 'home' }, { label: 'Inner page', value: 'inner' }]} onChange={(value) => setAttributes({ heroStyle: value })} />
                    <ToggleControl label="Show breadcrumbs" checked={showBreadcrumbs} onChange={(value) => setAttributes({ showBreadcrumbs: value })} />
                    {showBreadcrumbs && (
                        <>
                            <TextControl label="Home label" value={breadcrumbHomeLabel} onChange={(value) => setAttributes({ breadcrumbHomeLabel: value })} />
                            <TextControl label="Home URL" value={breadcrumbHomeUrl} onChange={(value) => setAttributes({ breadcrumbHomeUrl: value })} />
                            <TextControl label="Current page label" value={breadcrumbCurrentLabel} onChange={(value) => setAttributes({ breadcrumbCurrentLabel: value })} />
                        </>
                    )}
                    {!isInnerHero && <SelectControl label="Background color" value={background} options={[{ label: 'White', value: 'white' }, { label: 'Pale Blue', value: 'pale-blue' }, { label: 'Deep Blue', value: 'deep-blue' }]} onChange={(value) => setAttributes({ background: value })} />}
                    <TextControl label="Image alt text" value={imageAlt} onChange={(value) => setAttributes({ imageAlt: value })} />
                    <ToggleControl label="Show registration card" checked={showRegistration} onChange={(value) => setAttributes({ showRegistration: value })} />
                    {showRegistration && <TextControl label="Registration button URL" value={registrationButtonUrl} onChange={(value) => setAttributes({ registrationButtonUrl: value })} />}
                    <ToggleControl label="Show hero stats" checked={showStats} onChange={(value) => setAttributes({ showStats: value })} />
                    <ToggleControl label="Show primary button" checked={showPrimaryButton} onChange={(value) => setAttributes({ showPrimaryButton: value })} />
                    {showPrimaryButton && <TextControl label="Primary URL" value={primaryUrl} onChange={(value) => setAttributes({ primaryUrl: value })} />}
                    <ToggleControl label="Show secondary button" checked={showSecondaryButton} onChange={(value) => setAttributes({ showSecondaryButton: value })} />
                    {showSecondaryButton && <TextControl label="Secondary URL" value={secondaryUrl} onChange={(value) => setAttributes({ secondaryUrl: value })} />}
                </PanelBody>
            </InspectorControls>
            <section className={heroClass}>
                {showBreadcrumbs && (
                    <div className="sb-hero__breadcrumb-bar">
                        <nav className="sb-container sb-hero__breadcrumbs" aria-label="Breadcrumb">
                            <span>{breadcrumbHomeLabel}</span>
                            <span className="sb-hero__breadcrumb-separator" aria-hidden="true">›</span>
                            <span aria-current="page">{breadcrumbCurrentLabel}</span>
                        </nav>
                    </div>
                )}
                {isInnerHero ? (
                    <div className="sb-hero__inner">
                        {imageUrl && <img className="sb-hero__inner-image" src={imageUrl} alt={imageAlt} />}
                        <span className="sb-hero__inner-overlay" aria-hidden="true" />
                        <div className="sb-container sb-hero__inner-content">
                            {heroCopy}
                            <div className="sb-hero__inner-editor-actions">{imageControls}</div>
                        </div>
                    </div>
                ) : (
                    <div className="sb-container sb-hero__grid">
                        <div>{heroCopy}</div>
                        <div className="sb-editor-media">
                            {imageUrl && <img className="sb-hero__image" src={imageUrl} alt={imageAlt} />}
                            {imageControls}
                            {showRegistration && (
                                <div className="sb-hero__registration">
                                    <RichText tagName="span" className="sb-hero__registration-status" value={registrationStatus} placeholder="Registration Open" onChange={(value) => setAttributes({ registrationStatus: value })} />
                                    <RichText tagName="strong" className="sb-hero__registration-title" value={registrationTitle} placeholder="Program title" onChange={(value) => setAttributes({ registrationTitle: value })} />
                                    <RichText tagName="span" className="sb-hero__registration-details" value={registrationDetails} placeholder="Dates and location" onChange={(value) => setAttributes({ registrationDetails: value })} />
                                    <RichText tagName="span" className="sb-hero__registration-button" value={registrationButtonLabel} placeholder="Button label" onChange={(value) => setAttributes({ registrationButtonLabel: value })} />
                                </div>
                            )}
                        </div>
                    </div>
                )}
            </section>
        </>
    );
}
