import { InnerBlocks, InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { getBlockTypes } from '@wordpress/blocks';
import { PanelBody, RangeControl, SelectControl } from '@wordpress/components';

export default function Edit({ attributes, setAttributes }) {
    const background = attributes.background || 'white';
    const contentWidth = attributes.contentWidth || 1200;
    const sectionClass = `sb-content-container sb-block-bg alignfull${background !== 'white' ? ` sb-block-bg--${background}` : ''}`;
    const blockProps = useBlockProps({ className: sectionClass });
    const contentStyle = { '--sb-content-container-width': `${contentWidth}px` };
    const allowedBlocks = getBlockTypes()
        .map((blockType) => blockType.name)
        .filter((blockName) => !blockName.startsWith('soundbridge/'));

    return <>
        <InspectorControls>
            <PanelBody title="Container settings">
                <SelectControl
                    label="Background color"
                    value={background}
                    options={[
                        { label: 'White', value: 'white' },
                        { label: 'Pale blue', value: 'pale-blue' },
                        { label: 'Dark blue', value: 'dark-blue' },
                    ]}
                    onChange={(value) => setAttributes({ background: value })}
                />
                <RangeControl
                    label="Maximum content width"
                    help={`${contentWidth}px`}
                    value={contentWidth}
                    min={480}
                    max={1320}
                    step={20}
                    onChange={(value) => setAttributes({ contentWidth: value || 1200 })}
                />
            </PanelBody>
        </InspectorControls>
        <section {...blockProps}>
            <div className="sb-container sb-content-container__content" style={contentStyle}>
                <InnerBlocks
                    allowedBlocks={allowedBlocks}
                    renderAppender={InnerBlocks.ButtonBlockAppender}
                />
            </div>
        </section>
    </>;
}
