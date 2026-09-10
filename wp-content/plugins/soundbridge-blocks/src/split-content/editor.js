import { InspectorControls, MediaUpload, MediaUploadCheck, RichText } from '@wordpress/block-editor';
import { Button, PanelBody, SelectControl, TextControl, ToggleControl } from '@wordpress/components';

export default function Edit({ attributes, setAttributes }) {
    const {
        eyebrow,
        heading,
        text,
        secondaryText,
        imageId,
        imageUrl,
        imageAlt,
        secondaryImageId,
        secondaryImageUrl,
        secondaryImageAlt,
        mediaOverlayType = 'image',
        overlayStatValue,
        overlayStatLabel,
        metrics = [],
        showButton,
        buttonLabel,
        buttonUrl,
        showSecondaryButton,
        secondaryButtonLabel,
        secondaryButtonUrl,
        reverse,
        background,
    } = attributes;
    const sectionClass = `sb-about sb-section alignfull${background === 'blue' ? ' sb-section--pale' : ''}${background === 'dark-blue' ? ' sb-block-bg--dark-blue' : ''}`;
    const gridClass = `sb-container sb-about__grid${reverse ? ' sb-about__grid--reverse' : ''}`;
    const selectPrimaryImage = (media) => setAttributes({
        imageId: media.id,
        imageUrl: media.url,
        imageAlt: media.alt || media.title || '',
    });
    const selectSecondaryImage = (media) => setAttributes({
        secondaryImageId: media.id,
        secondaryImageUrl: media.url,
        secondaryImageAlt: media.alt || media.title || '',
    });
    const updateMetric = (index, key, value) => {
        const nextMetrics = metrics.map((metric, metricIndex) =>
            metricIndex === index ? { ...metric, [key]: value } : metric
        );
        setAttributes({ metrics: nextMetrics });
    };

    const imageEditor = (url, alt, id, onSelect, onRemove, className, emptyLabel) => (
        <div className={`sb-editor-media ${className}`}>
            {url ? (
                <>
                    <img src={url} alt={alt} />
                    <div className="sb-editor-media__actions">
                        <MediaUploadCheck>
                            <MediaUpload allowedTypes={['image']} value={id} onSelect={onSelect} render={({ open }) => <Button variant="primary" onClick={open}>Replace image</Button>} />
                        </MediaUploadCheck>
                        <Button variant="secondary" isDestructive onClick={onRemove}>Remove image</Button>
                    </div>
                </>
            ) : (
                <MediaUploadCheck>
                    <MediaUpload allowedTypes={['image']} value={id} onSelect={onSelect} render={({ open }) => <Button variant="secondary" onClick={open}>{emptyLabel}</Button>} />
                </MediaUploadCheck>
            )}
        </div>
    );

    return (
        <>
            <InspectorControls>
                <PanelBody title="About section settings">
                    <SelectControl
                        label="Image position"
                        value={reverse ? 'right' : 'left'}
                        options={[
                            { label: 'Left — content on right', value: 'left' },
                            { label: 'Right — content on left', value: 'right' },
                        ]}
                        onChange={(value) => setAttributes({ reverse: value === 'right' })}
                    />
                    <SelectControl label="Background color" value={background} options={[{ label: 'White', value: 'white' }, { label: 'Pale blue', value: 'blue' }, { label: 'Dark blue', value: 'dark-blue' }]} onChange={(value) => setAttributes({ background: value })} />
                    <SelectControl
                        label="Image overlay"
                        value={mediaOverlayType}
                        options={[
                            { label: 'Second image', value: 'image' },
                            { label: 'Impact stat', value: 'stat' },
                            { label: 'None', value: 'none' },
                        ]}
                        onChange={(value) => setAttributes({ mediaOverlayType: value })}
                    />
                    <TextControl label="Main image alt text" value={imageAlt} onChange={(value) => setAttributes({ imageAlt: value })} />
                    {mediaOverlayType === 'image' && <TextControl label="Secondary image alt text" value={secondaryImageAlt} onChange={(value) => setAttributes({ secondaryImageAlt: value })} />}
                    <ToggleControl label="Show buttons" checked={showButton} onChange={(value) => setAttributes({ showButton: value })} />
                    {showButton && (
                        <>
                            <TextControl label="Primary button URL" value={buttonUrl} onChange={(value) => setAttributes({ buttonUrl: value })} />
                            <ToggleControl label="Show secondary button" checked={showSecondaryButton} onChange={(value) => setAttributes({ showSecondaryButton: value })} />
                            {showSecondaryButton && <TextControl label="Secondary button URL" value={secondaryButtonUrl} onChange={(value) => setAttributes({ secondaryButtonUrl: value })} />}
                        </>
                    )}
                </PanelBody>
            </InspectorControls>
            <section className={sectionClass}>
                <div className={gridClass}>
                    <div className="sb-about__media">
                        {imageEditor(imageUrl, imageAlt, imageId, selectPrimaryImage, () => setAttributes({ imageId: 0, imageUrl: '', imageAlt: '' }), 'sb-about__image--primary', 'Choose main image')}
                        {mediaOverlayType === 'image' && imageEditor(secondaryImageUrl, secondaryImageAlt, secondaryImageId, selectSecondaryImage, () => setAttributes({ secondaryImageId: 0, secondaryImageUrl: '', secondaryImageAlt: '' }), 'sb-about__image--secondary', 'Choose secondary image')}
                        {mediaOverlayType === 'stat' && (
                            <div className="sb-about__overlay-stat">
                                <RichText tagName="strong" value={overlayStatValue} placeholder="1,000+" onChange={(value) => setAttributes({ overlayStatValue: value })} />
                                <RichText tagName="span" value={overlayStatLabel} placeholder="Students & community members impacted" onChange={(value) => setAttributes({ overlayStatLabel: value })} />
                            </div>
                        )}
                    </div>
                    <div className="sb-about__content">
                        <RichText tagName="p" className="sb-about__badge" value={eyebrow} placeholder="Since 1999" onChange={(value) => setAttributes({ eyebrow: value })} />
                        <RichText tagName="h2" value={heading} placeholder="About heading" onChange={(value) => setAttributes({ heading: value })} />
                        <RichText tagName="p" className="sb-copy" value={text} placeholder="About introduction" onChange={(value) => setAttributes({ text: value })} />
                        <RichText tagName="p" className="sb-copy sb-about__secondary-copy" value={secondaryText} placeholder="Add optional secondary text…" onChange={(value) => setAttributes({ secondaryText: value })} />
                        <div className="sb-about__metrics">
                            {metrics.map((metric, index) => (
                                <div className="sb-about__metric" key={index}>
                                    <RichText tagName="strong" value={metric.value} placeholder="30%" onChange={(value) => updateMetric(index, 'value', value)} />
                                    <RichText tagName="span" className="sb-about__metric-label" value={metric.label} placeholder="Metric label" onChange={(value) => updateMetric(index, 'label', value)} />
                                    <RichText tagName="small" value={metric.detail} placeholder="Metric detail" onChange={(value) => updateMetric(index, 'detail', value)} />
                                    <div className="sb-about__progress"><span style={{ width: `${metric.progress || 0}%` }} /></div>
                                    <TextControl label="Progress percentage" type="number" min={0} max={100} value={metric.progress} onChange={(value) => updateMetric(index, 'progress', Math.min(100, Math.max(0, parseInt(value || '0', 10))))} />
                                    <Button isDestructive variant="link" onClick={() => setAttributes({ metrics: metrics.filter((_, metricIndex) => metricIndex !== index) })}>Remove metric</Button>
                                </div>
                            ))}
                        </div>
                        <div className="sb-about__editor-actions">
                            <Button variant="secondary" onClick={() => setAttributes({ metrics: [...metrics, { value: '', label: '', detail: '', progress: 0 }] })}>Add metric</Button>
                        </div>
                        {showButton && (
                            <div className="sb-about__buttons">
                                <RichText tagName="span" className="sb-btn sb-btn--outline sb-about__button" value={buttonLabel} placeholder="Primary button label" onChange={(value) => setAttributes({ buttonLabel: value })} />
                                {showSecondaryButton && <RichText tagName="span" className="sb-btn sb-about__button" value={secondaryButtonLabel} placeholder="Secondary button label" onChange={(value) => setAttributes({ secondaryButtonLabel: value })} />}
                            </div>
                        )}
                    </div>
                </div>
            </section>
        </>
    );
}
