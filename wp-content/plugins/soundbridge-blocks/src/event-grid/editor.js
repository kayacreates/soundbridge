import ServerSideRender from '@wordpress/server-side-render';
import { InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';

export default function Edit({ attributes, setAttributes }) {
    return (
        <>
            <InspectorControls>
                <PanelBody title="Event grid settings">
                    <TextControl label="Heading" value={attributes.heading} onChange={(value) => setAttributes({ heading: value })} />
                    <TextControl label="Number of events" type="number" min={1} value={attributes.count} onChange={(value) => setAttributes({ count: Math.max(1, parseInt(value || '1', 10)) })} />
                </PanelBody>
            </InspectorControls>
            <ServerSideRender block="soundbridge/event-grid" attributes={attributes} />
        </>
    );
}
