import ServerSideRender from '@wordpress/server-side-render';
import { InspectorControls, RichText } from '@wordpress/block-editor';
import { PanelBody, SelectControl, TextControl } from '@wordpress/components';

export default function Edit({ attributes, setAttributes }) {
    return (
        <>
            <InspectorControls>
                <PanelBody title="Program grid settings">
                    <SelectControl label="Background color" value={attributes.background || 'pale-blue'} options={[{ label: 'White', value: 'white' }, { label: 'Pale blue', value: 'pale-blue' }, { label: 'Dark blue', value: 'dark-blue' }]} onChange={(value) => setAttributes({ background: value })} />
                    <p className="sb-editor-field-label">Heading</p>
                    <RichText tagName="div" className="sb-editor-rich-heading" value={attributes.heading} placeholder="Section heading" onChange={(value) => setAttributes({ heading: value })} />
                    <TextControl label="Number of programs" type="number" min={1} value={attributes.count} onChange={(value) => setAttributes({ count: Math.max(1, parseInt(value || '1', 10)) })} />
                </PanelBody>
            </InspectorControls>
            <ServerSideRender block="soundbridge/program-grid" attributes={attributes} />
        </>
    );
}
