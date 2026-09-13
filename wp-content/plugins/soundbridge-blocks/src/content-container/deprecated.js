import { InnerBlocks, useBlockProps } from '@wordpress/block-editor';

const attributes = {
    background: {
        type: 'string',
        enum: ['white', 'pale-blue', 'dark-blue'],
        default: 'white',
    },
    contentWidth: {
        type: 'number',
        default: 720,
    },
};

export default [{
    attributes,
    migrate(blockAttributes) {
        return {
            ...blockAttributes,
            contentWidth: blockAttributes.contentWidth || 1200,
        };
    },
    save({ attributes: blockAttributes }) {
        const background = blockAttributes.background || 'white';
        const contentWidth = blockAttributes.contentWidth || 720;
        const sectionClass = `sb-content-container sb-block-bg alignfull${background !== 'white' ? ` sb-block-bg--${background}` : ''}`;
        const contentStyle = { '--sb-content-container-width': `${contentWidth}px` };

        return <section {...useBlockProps.save({ className: sectionClass })}>
            <div className="sb-container sb-content-container__content" style={contentStyle}>
                <InnerBlocks.Content />
            </div>
        </section>;
    },
}];
