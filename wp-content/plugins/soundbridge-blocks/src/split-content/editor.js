import { InspectorControls, MediaUpload, MediaUploadCheck, RichText } from '@wordpress/block-editor';
import { Button, PanelBody, SelectControl, TextControl, ToggleControl } from '@wordpress/components';

export default function Edit({ attributes, setAttributes }) {
    const { eyebrow, heading, text, imageId, imageUrl, imageAlt, reverse, background } = attributes;
    const sectionClass = `sb-section alignfull${background === 'blue' ? ' sb-section--pale' : ''}`;
    const splitClass = `sb-container sb-split${reverse ? ' sb-split--reverse' : ''}`;
    const selectImage = (media) => setAttributes({
        imageId: media.id,
        imageUrl: media.url,
        imageAlt: media.alt || media.title || '',
    });
    const removeImage = () => setAttributes({ imageId: 0, imageUrl: '', imageAlt: '' });

    return (
        <>
            <InspectorControls>
                <PanelBody title="Layout">
                    <ToggleControl label="Image first" checked={reverse} onChange={(value) => setAttributes({ reverse: value })} />
                    <SelectControl label="Background" value={background} options={[{ label: 'White', value: 'white' }, { label: 'Pale blue', value: 'blue' }]} onChange={(value) => setAttributes({ background: value })} />
                    <TextControl label="Image alt text" value={imageAlt} onChange={(value) => setAttributes({ imageAlt: value })} />
                </PanelBody>
            </InspectorControls>
            <section className={sectionClass}>
                <div className={splitClass}>
                    <div>
                        <RichText tagName="p" className="sb-eyebrow" value={eyebrow} placeholder="Eyebrow" onChange={(value) => setAttributes({ eyebrow: value })} />
                        <RichText tagName="h2" value={heading} placeholder="Heading" onChange={(value) => setAttributes({ heading: value })} />
                        <RichText tagName="p" className="sb-copy" value={text} placeholder="Body text" onChange={(value) => setAttributes({ text: value })} />
                    </div>
                    <div className="sb-editor-media">
                        {imageUrl ? (
                            <>
                                <img src={imageUrl} alt={imageAlt} />
                                <div className="sb-editor-media__actions">
                                    <MediaUploadCheck>
                                        <MediaUpload allowedTypes={['image']} value={imageId} onSelect={selectImage} render={({ open }) => <Button variant="primary" onClick={open}>Replace image</Button>} />
                                    </MediaUploadCheck>
                                    <Button variant="secondary" isDestructive onClick={removeImage}>Remove image</Button>
                                </div>
                            </>
                        ) : (
                            <MediaUploadCheck>
                                <MediaUpload allowedTypes={['image']} value={imageId} onSelect={selectImage} render={({ open }) => <Button variant="secondary" onClick={open}>Choose image</Button>} />
                            </MediaUploadCheck>
                        )}
                    </div>
                </div>
            </section>
        </>
    );
}
