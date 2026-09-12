import { InspectorControls, RichText, URLInput, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, SelectControl, ToggleControl } from '@wordpress/components';

export default function Edit({ attributes, setAttributes }) {
    const { background = 'dark-blue', showButton, heading, text, buttonLabel, buttonUrl, showSecondaryButton, secondaryButtonLabel, secondaryButtonUrl } = attributes;
    const sectionClass = `sb-cta alignfull sb-block-bg--${background}`;

    return (
        <>
            <InspectorControls>
                <PanelBody title="Button settings">
                    <SelectControl label="Background color" value={background} options={[{ label: 'Pale blue', value: 'pale-blue' }, { label: 'Blue', value: 'blue' }]} onChange={(value) => setAttributes({ background: value })} />
                    <ToggleControl label="Show buttons" checked={showButton} onChange={(value) => setAttributes({ showButton: value })} />
                    {showButton && (
                        <>
                            <div><p className="sb-editor-field-label">Primary button link</p><URLInput value={buttonUrl} onChange={(value) => setAttributes({ buttonUrl: value })} /></div>
                            <ToggleControl label="Show secondary button" checked={showSecondaryButton} onChange={(value) => setAttributes({ showSecondaryButton: value })} />
                            {showSecondaryButton && <div><p className="sb-editor-field-label">Secondary button link</p><URLInput value={secondaryButtonUrl} onChange={(value) => setAttributes({ secondaryButtonUrl: value })} /></div>}
                        </>
                    )}
                </PanelBody>
            </InspectorControls>
            <section {...useBlockProps({ className: sectionClass })}>
                <div className="sb-container">
                    <RichText tagName="h2" value={heading} placeholder="CTA heading" onChange={(value) => setAttributes({ heading: value })} />
                    <RichText tagName="p" value={text} placeholder="CTA text" onChange={(value) => setAttributes({ text: value })} />
                    {showButton && (
                        <div className="sb-cta__buttons">
                            <RichText tagName="span" className="sb-btn" value={buttonLabel} placeholder="Primary button label" onChange={(value) => setAttributes({ buttonLabel: value })} />
                            {showSecondaryButton && <RichText tagName="span" className="sb-btn sb-btn--outline" value={secondaryButtonLabel} placeholder="Secondary button label" onChange={(value) => setAttributes({ secondaryButtonLabel: value })} />}
                        </div>
                    )}
                </div>
            </section>
        </>
    );
}
