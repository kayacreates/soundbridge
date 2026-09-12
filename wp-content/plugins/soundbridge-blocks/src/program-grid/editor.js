import ServerSideRender from '@wordpress/server-side-render';
import { InspectorControls, RichText, URLInput, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, SelectControl, TextControl, ToggleControl } from '@wordpress/components';

export default function Edit({ attributes, setAttributes }) {
    const background = attributes.background || 'pale-blue';
    const sectionClass = `sb-program-grid-section sb-block-bg alignfull sb-block-bg--${background}`;
    const headingFormats = ['core/bold', 'core/italic', 'soundbridge/pale-blue-highlight'];

    return (
        <>
            <InspectorControls>
                <PanelBody title="Program grid settings">
                    <SelectControl label="Background color" value={attributes.background || 'pale-blue'} options={[{ label: 'White', value: 'white' }, { label: 'Pale blue', value: 'pale-blue' }, { label: 'Dark blue', value: 'dark-blue' }]} onChange={(value) => setAttributes({ background: value })} />
                    <TextControl label="Number of programs" type="number" min={1} value={attributes.count} onChange={(value) => setAttributes({ count: Math.max(1, parseInt(value || '1', 10)) })} />
                    <ToggleControl label="Include child program options" checked={attributes.includeChildPrograms} onChange={(value) => setAttributes({ includeChildPrograms: value })} />
                    <ToggleControl label="Show View All Programs button" checked={attributes.showViewAll} onChange={(value) => setAttributes({ showViewAll: value })} />
                    {attributes.showViewAll && <><TextControl label="Button label" value={attributes.viewAllLabel} onChange={(value) => setAttributes({ viewAllLabel: value })} /><p className="sb-editor-field-label">Button link</p><URLInput value={attributes.viewAllUrl} onChange={(value) => setAttributes({ viewAllUrl: value })} /></>}
                </PanelBody>
            </InspectorControls>
            <section {...useBlockProps({ className: sectionClass })}>
                <div className="sb-container">
                    <header className="sb-program-grid-section__header">
                        <div>
                            <RichText
                                tagName="p"
                                className="sb-eyebrow"
                                value={attributes.eyebrow}
                                allowedFormats={['soundbridge/pale-blue-highlight']}
                                placeholder="Our Programs"
                                onChange={(value) => setAttributes({ eyebrow: value })}
                            />
                            <RichText
                                tagName="h2"
                                value={attributes.heading}
                                allowedFormats={headingFormats}
                                placeholder="Program grid heading"
                                onChange={(value) => setAttributes({ heading: value })}
                            />
                        </div>
                        {attributes.showViewAll && (
                            <RichText
                                tagName="span"
                                className="sb-btn sb-btn--outline sb-program-grid-section__view-all"
                                value={attributes.viewAllLabel}
                                allowedFormats={[]}
                                placeholder="View All Programs →"
                                onChange={(value) => setAttributes({ viewAllLabel: value })}
                            />
                        )}
                    </header>
                </div>
                <ServerSideRender block="soundbridge/program-grid" attributes={{ ...attributes, editorPreview: true }} />
            </section>
        </>
    );
}
