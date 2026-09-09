import { InspectorControls, RichText } from '@wordpress/block-editor';
import { PanelBody, TextControl, ToggleControl } from '@wordpress/components';

export default function Edit({ attributes, setAttributes }) {
    const { showButton, heading, text, buttonLabel, buttonUrl } = attributes;

    return (
        <>
            <InspectorControls>
                <PanelBody title="Button settings">
                    <ToggleControl label="Show button" checked={showButton} onChange={(value) => setAttributes({ showButton: value })} />
                    {showButton && <TextControl label="Button URL" value={buttonUrl} onChange={(value) => setAttributes({ buttonUrl: value })} />}
                </PanelBody>
            </InspectorControls>
            <section className="sb-cta alignfull">
                <div className="sb-container">
                    <RichText tagName="h2" value={heading} placeholder="CTA heading" onChange={(value) => setAttributes({ heading: value })} />
                    <RichText tagName="p" value={text} placeholder="CTA text" onChange={(value) => setAttributes({ text: value })} />
                    {showButton && <RichText tagName="span" className="sb-btn sb-btn--light" value={buttonLabel} placeholder="Button label" onChange={(value) => setAttributes({ buttonLabel: value })} />}
                </div>
            </section>
        </>
    );
}
