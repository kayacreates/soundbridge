import ServerSideRender from '@wordpress/server-side-render';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, ToggleControl } from '@wordpress/components';

export default function Edit({ attributes, setAttributes }) {
    return (
        <>
            <InspectorControls>
                <PanelBody title="Events settings">
                    <TextControl label="Eyebrow" value={attributes.eyebrow} onChange={(value) => setAttributes({ eyebrow: value })} />
                    <TextControl label="Heading" value={attributes.heading} onChange={(value) => setAttributes({ heading: value })} />
                    <TextControl label="Number of events" type="number" min={1} value={attributes.count} onChange={(value) => setAttributes({ count: Math.max(1, parseInt(value || '1', 10)) })} />
                    <ToggleControl label="Show events button" checked={attributes.showButton} onChange={(value) => setAttributes({ showButton: value })} />
                    {attributes.showButton && (
                        <>
                            <TextControl label="Button label" value={attributes.buttonLabel} onChange={(value) => setAttributes({ buttonLabel: value })} />
                            <TextControl label="Button URL" value={attributes.buttonUrl} onChange={(value) => setAttributes({ buttonUrl: value })} />
                        </>
                    )}
                </PanelBody>
            </InspectorControls>
            <ServerSideRender block="soundbridge/event-grid" attributes={attributes} />
        </>
    );
}
