import { InspectorControls, MediaUpload, MediaUploadCheck, RichText, URLInput } from '@wordpress/block-editor';
import { Button, PanelBody, SelectControl, TextControl, ToggleControl } from '@wordpress/components';

export default function Edit({ attributes, setAttributes }) {
    const {
        background = 'white',
        eyebrow,
        heading,
        imageId = 0,
        imageUrl = '',
        imageAlt = '',
        showButton = true,
        buttonLabel,
        buttonUrl,
        items = [],
    } = attributes;
    const sectionClass = `sb-faq sb-block-bg alignfull${background !== 'white' ? ` sb-block-bg--${background}` : ''}`;
    const updateItem = (index, key, value) => {
        const nextItems = items.map((item, itemIndex) =>
            itemIndex === index ? { ...item, [key]: value } : item
        );
        setAttributes({ items: nextItems });
    };

    return (
        <>
        <InspectorControls>
            <PanelBody title="FAQ settings">
                <SelectControl label="Background color" value={background} options={[{ label: 'White', value: 'white' }, { label: 'Pale blue', value: 'pale-blue' }, { label: 'Dark blue', value: 'dark-blue' }]} onChange={(value) => setAttributes({ background: value })} />
                <TextControl label="Image alt text" value={imageAlt} onChange={(value) => setAttributes({ imageAlt: value })} />
                <ToggleControl label="Show View All button" checked={showButton} onChange={(value) => setAttributes({ showButton: value })} />
                {showButton && <div><p className="sb-editor-field-label">Button link</p><URLInput value={buttonUrl} onChange={(value) => setAttributes({ buttonUrl: value })} /></div>}
            </PanelBody>
        </InspectorControls>
        <section className={sectionClass}>
            <div className="sb-container sb-faq__grid">
                <div className="sb-faq__media">
                    {imageUrl && <img src={imageUrl} alt={imageAlt} />}
                    <div className="sb-faq__media-actions">
                        <MediaUploadCheck>
                            <MediaUpload
                                allowedTypes={['image']}
                                value={imageId}
                                onSelect={(image) => setAttributes({ imageId: image.id, imageUrl: image.url, imageAlt: image.alt || image.title || '' })}
                                render={({ open }) => <Button variant={imageUrl ? 'primary' : 'secondary'} onClick={open}>{imageUrl ? 'Replace image' : 'Choose image'}</Button>}
                            />
                        </MediaUploadCheck>
                        {imageUrl && <Button variant="secondary" isDestructive onClick={() => setAttributes({ imageId: 0, imageUrl: '', imageAlt: '' })}>Remove image</Button>}
                    </div>
                </div>
                <div className="sb-faq__content">
                    <RichText tagName="p" className="sb-badge" value={eyebrow} placeholder="Eyebrow" onChange={(value) => setAttributes({ eyebrow: value })} />
                    <RichText tagName="h2" value={heading} placeholder="Heading" onChange={(value) => setAttributes({ heading: value })} />
                    <div className="sb-faq__list">
                        {items.map((item, index) => (
                            <details open key={index}>
                                <summary>
                                    <RichText tagName="span" value={item.q} placeholder="Question" onChange={(value) => updateItem(index, 'q', value)} />
                                    <span className="sb-faq__chevron" aria-hidden="true" />
                                </summary>
                                <div className="sb-faq__answer"><RichText tagName="p" value={item.a} placeholder="Answer" onChange={(value) => updateItem(index, 'a', value)} /></div>
                                <Button className="sb-faq__remove" isDestructive variant="link" onClick={() => setAttributes({ items: items.filter((_, itemIndex) => itemIndex !== index) })}>Remove question</Button>
                            </details>
                        ))}
                    </div>
                    <Button className="sb-faq__add" variant="secondary" onClick={() => setAttributes({ items: [...items, { q: '', a: '' }] })}>Add question</Button>
                    {showButton && <RichText tagName="span" className="sb-btn sb-btn--outline sb-faq__button" value={buttonLabel} placeholder="Button label" onChange={(value) => setAttributes({ buttonLabel: value })} />}
                </div>
            </div>
        </section>
        </>
    );
}
