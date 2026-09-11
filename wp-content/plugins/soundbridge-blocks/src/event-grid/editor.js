import ServerSideRender from '@wordpress/server-side-render';
import { InspectorControls, RichText, URLInput } from '@wordpress/block-editor';
import { PanelBody, SelectControl, TextControl, ToggleControl } from '@wordpress/components';

export default function Edit({ attributes, setAttributes }) {
    return (
        <>
            <InspectorControls>
                <PanelBody title="Events settings">
                    <SelectControl label="Background color" value={attributes.background || 'pale-blue'} options={[{ label: 'White', value: 'white' }, { label: 'Pale blue', value: 'pale-blue' }, { label: 'Dark blue', value: 'dark-blue' }]} onChange={(value) => setAttributes({ background: value })} />
                    <TextControl label="Eyebrow" value={attributes.eyebrow} onChange={(value) => setAttributes({ eyebrow: value })} />
                    <p className="sb-editor-field-label">Heading</p>
                    <RichText tagName="div" className="sb-editor-rich-heading" value={attributes.heading} placeholder="Section heading" onChange={(value) => setAttributes({ heading: value })} />
                    <TextControl label="Number of events" type="number" min={1} value={attributes.count} onChange={(value) => setAttributes({ count: Math.max(1, parseInt(value || '1', 10)) })} />
                    <ToggleControl label="Show events button" checked={attributes.showButton} onChange={(value) => setAttributes({ showButton: value })} />
                    {attributes.showButton && (
                        <>
                            <TextControl label="Button label" value={attributes.buttonLabel} onChange={(value) => setAttributes({ buttonLabel: value })} />
                            <div><p className="sb-editor-field-label">Button link</p><URLInput value={attributes.buttonUrl} onChange={(value) => setAttributes({ buttonUrl: value })} /></div>
                        </>
                    )}
                </PanelBody>
            </InspectorControls>
            <ServerSideRender block="soundbridge/event-grid" attributes={attributes} />
        </>
    );
}
