import ServerSideRender from '@wordpress/server-side-render';
import { InspectorControls, RichText, URLInput, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, RangeControl, SelectControl, TextControl, ToggleControl } from '@wordpress/components';

export default function Edit({ attributes, setAttributes }) {
    const background = attributes.background || 'white';
    const sectionClass = `sb-faculty-grid-section sb-block-bg alignfull sb-block-bg--${background}`;

    return <>
        <InspectorControls>
            <PanelBody title="Faculty grid settings">
                <SelectControl label="Background color" value={background} options={[{ label: 'White', value: 'white' }, { label: 'Pale blue', value: 'pale-blue' }, { label: 'Dark blue', value: 'dark-blue' }]} onChange={(value) => setAttributes({ background: value })} />
                <SelectControl label="Columns" value={attributes.columns || 4} options={[2, 3, 4].map((value) => ({ label: String(value), value }))} onChange={(value) => setAttributes({ columns: Number(value) })} />
                <RangeControl label="Number of faculty" value={attributes.count || 4} min={1} max={12} onChange={(value) => setAttributes({ count: value || 4 })} />
                <ToggleControl label="Show Meet All Faculty button" checked={attributes.showViewAll} onChange={(value) => setAttributes({ showViewAll: value })} />
                {attributes.showViewAll && <><TextControl label="Button label" value={attributes.viewAllLabel} onChange={(value) => setAttributes({ viewAllLabel: value })} /><p className="sb-editor-field-label">Button link</p><URLInput value={attributes.viewAllUrl} onChange={(value) => setAttributes({ viewAllUrl: value })} /></>}
            </PanelBody>
        </InspectorControls>
        <section {...useBlockProps({ className: sectionClass })}>
            <div className="sb-container">
                <header className="sb-faculty-grid-section__header">
                    <div>
                        <RichText tagName="p" className="sb-eyebrow" value={attributes.eyebrow} placeholder="Our Educators" onChange={(value) => setAttributes({ eyebrow: value })} />
                        <RichText tagName="h2" value={attributes.heading} allowedFormats={['core/bold', 'core/italic', 'soundbridge/pale-blue-highlight']} placeholder="Meet Our Faculty" onChange={(value) => setAttributes({ heading: value })} />
                    </div>
                    {attributes.showViewAll && <RichText tagName="span" className="sb-btn sb-btn--outline" value={attributes.viewAllLabel} allowedFormats={[]} onChange={(value) => setAttributes({ viewAllLabel: value })} />}
                </header>
            </div>
            <ServerSideRender block="soundbridge/faculty-grid" attributes={{ ...attributes, editorPreview: true }} />
        </section>
    </>;
}
