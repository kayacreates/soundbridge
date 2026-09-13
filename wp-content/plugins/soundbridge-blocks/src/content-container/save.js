import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

export default function save({ attributes }) {
    const background = attributes.background || 'white';
    const contentWidth = attributes.contentWidth || 1200;
    const sectionClass = `sb-content-container sb-block-bg alignfull${background !== 'white' ? ` sb-block-bg--${background}` : ''}`;
    const contentStyle = { '--sb-content-container-width': `${contentWidth}px` };

    return <section {...useBlockProps.save({ className: sectionClass })}>
        <div className="sb-container sb-content-container__content" style={contentStyle}>
            <InnerBlocks.Content />
        </div>
    </section>;
}
