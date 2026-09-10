import { InspectorControls, RichText } from '@wordpress/block-editor';
import { PanelBody, SelectControl, TextControl, ToggleControl } from '@wordpress/components';

export default function Edit({ attributes, setAttributes }) {
    const { background = 'dark-blue', showButton, heading, text, buttonLabel, buttonUrl, showSecondaryButton, secondaryButtonLabel, secondaryButtonUrl } = attributes;
    const sectionClass = `sb-cta alignfull${background !== 'dark-blue' ? ` sb-block-bg--${background}` : ''}`;

    return (
        <>
            <InspectorControls>
                <PanelBody title="Button settings">
                    <SelectControl label="Background color" value={background} options={[{ label: 'White', value: 'white' }, { label: 'Pale blue', value: 'pale-blue' }, { label: 'Dark blue', value: 'dark-blue' }]} onChange={(value) => setAttributes({ background: value })} />
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
                <div className="sb-container">
                    <RichText tagName="h2" value={heading} placeholder="CTA heading" onChange={(value) => setAttributes({ heading: value })} />
                    <RichText tagName="p" value={text} placeholder="CTA text" onChange={(value) => setAttributes({ text: value })} />
                    {showButton && (
                        <div className="sb-cta__buttons">
                            <RichText tagName="span" className="sb-btn sb-btn--light" value={buttonLabel} placeholder="Primary button label" onChange={(value) => setAttributes({ buttonLabel: value })} />
                            {showSecondaryButton && <RichText tagName="span" className="sb-btn sb-cta__secondary" value={secondaryButtonLabel} placeholder="Secondary button label" onChange={(value) => setAttributes({ secondaryButtonLabel: value })} />}
                        </div>
                    )}
                </div>
            </section>
        </>
    );
}
