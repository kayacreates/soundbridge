import { InspectorControls, MediaUpload, MediaUploadCheck, RichText } from '@wordpress/block-editor';
import { Button, PanelBody, TextControl, ToggleControl } from '@wordpress/components';

export default function Edit({ attributes, setAttributes }) {
    const { eyebrow, heading, text, imageId, imageUrl, imageAlt, showPrimaryButton, primaryLabel, primaryUrl, showSecondaryButton, secondaryLabel, secondaryUrl } = attributes;
    const selectImage = (media) => setAttributes({
        imageId: media.id,
        imageUrl: media.url,
        imageAlt: media.alt || media.title || '',
    });
    const removeImage = () => setAttributes({ imageId: 0, imageUrl: '', imageAlt: '' });

    return (
        <>
            <InspectorControls>
                <PanelBody title="Hero settings">
                    <TextControl label="Image alt text" value={imageAlt} onChange={(value) => setAttributes({ imageAlt: value })} />
                    <ToggleControl label="Show primary button" checked={showPrimaryButton} onChange={(value) => setAttributes({ showPrimaryButton: value })} />
                    {showPrimaryButton && <TextControl label="Primary URL" value={primaryUrl} onChange={(value) => setAttributes({ primaryUrl: value })} />}
                    <ToggleControl label="Show secondary button" checked={showSecondaryButton} onChange={(value) => setAttributes({ showSecondaryButton: value })} />
                    {showSecondaryButton && <TextControl label="Secondary URL" value={secondaryUrl} onChange={(value) => setAttributes({ secondaryUrl: value })} />}
                </PanelBody>
            </InspectorControls>
            <section className="sb-hero alignfull">
                <div className="sb-container sb-hero__grid">
                    <div>
                        <RichText tagName="p" className="sb-eyebrow" value={eyebrow} placeholder="Eyebrow" onChange={(value) => setAttributes({ eyebrow: value })} />
                        <RichText tagName="h1" value={heading} placeholder="Hero heading" onChange={(value) => setAttributes({ heading: value })} />
                        <RichText tagName="p" className="sb-lead" value={text} placeholder="Hero text" onChange={(value) => setAttributes({ text: value })} />
                        {(showPrimaryButton || showSecondaryButton) && (
                            <div className="sb-actions">
                                {showPrimaryButton && <RichText tagName="span" className="sb-btn" value={primaryLabel} placeholder="Primary label" onChange={(value) => setAttributes({ primaryLabel: value })} />}
                                {showSecondaryButton && <RichText tagName="span" className="sb-btn sb-btn--outline" value={secondaryLabel} placeholder="Secondary label" onChange={(value) => setAttributes({ secondaryLabel: value })} />}
                            </div>
                        )}
                    </div>
                    <div className="sb-editor-media">
                        {imageUrl ? (
                            <>
                                <img className="sb-hero__image" src={imageUrl} alt={imageAlt} />
                                <div className="sb-editor-media__actions">
                                    <MediaUploadCheck>
                                        <MediaUpload allowedTypes={['image']} value={imageId} onSelect={selectImage} render={({ open }) => <Button variant="primary" onClick={open}>Replace image</Button>} />
                                    </MediaUploadCheck>
                                    <Button variant="secondary" isDestructive onClick={removeImage}>Remove image</Button>
                                </div>
                            </>
                        ) : (
                            <MediaUploadCheck>
                                <MediaUpload allowedTypes={['image']} value={imageId} onSelect={selectImage} render={({ open }) => <Button variant="secondary" onClick={open}>Choose hero image</Button>} />
                            </MediaUploadCheck>
                        )}
                    </div>
                </div>
            </section>
        </>
    );
}
