import { InspectorControls, MediaUpload, MediaUploadCheck, RichText } from '@wordpress/block-editor';
import { Button, PanelBody, SelectControl, TextControl, ToggleControl } from '@wordpress/components';

export default function Edit({ attributes, setAttributes }) {
    const {
        eyebrow,
        heading,
        text,
        imageId,
        imageUrl,
        imageAlt,
        secondaryImageId,
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
                    <ToggleControl label="Images first" checked={!reverse} onChange={(value) => setAttributes({ reverse: !value })} />
                    <SelectControl label="Background" value={background} options={[{ label: 'White', value: 'white' }, { label: 'Pale blue', value: 'blue' }]} onChange={(value) => setAttributes({ background: value })} />
                    <TextControl label="Main image alt text" value={imageAlt} onChange={(value) => setAttributes({ imageAlt: value })} />
                    <TextControl label="Secondary image alt text" value={secondaryImageAlt} onChange={(value) => setAttributes({ secondaryImageAlt: value })} />
                    <ToggleControl label="Show story button" checked={showButton} onChange={(value) => setAttributes({ showButton: value })} />
                    {showButton && <TextControl label="Button URL" value={buttonUrl} onChange={(value) => setAttributes({ buttonUrl: value })} />}
                </PanelBody>
            </InspectorControls>
            <section className={sectionClass}>
                <div className={gridClass}>
                    <div className="sb-about__media">
                        {imageEditor(imageUrl, imageAlt, imageId, selectPrimaryImage, () => setAttributes({ imageId: 0, imageUrl: '', imageAlt: '' }), 'sb-about__image--primary', 'Choose main image')}
                        {imageEditor(secondaryImageUrl, secondaryImageAlt, secondaryImageId, selectSecondaryImage, () => setAttributes({ secondaryImageId: 0, secondaryImageUrl: '', secondaryImageAlt: '' }), 'sb-about__image--secondary', 'Choose secondary image')}
                    </div>
                    <div className="sb-about__content">
                        <RichText tagName="p" className="sb-about__badge" value={eyebrow} placeholder="Since 1999" onChange={(value) => setAttributes({ eyebrow: value })} />
                        <RichText tagName="h2" value={heading} placeholder="About heading" onChange={(value) => setAttributes({ heading: value })} />
                        <RichText tagName="p" className="sb-copy" value={text} placeholder="About introduction" onChange={(value) => setAttributes({ text: value })} />
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
                        {showButton && <RichText tagName="span" className="sb-btn sb-btn--outline sb-about__button" value={buttonLabel} placeholder="Button label" onChange={(value) => setAttributes({ buttonLabel: value })} />}
                    </div>
                </div>
            </section>
        </>
    );
}
