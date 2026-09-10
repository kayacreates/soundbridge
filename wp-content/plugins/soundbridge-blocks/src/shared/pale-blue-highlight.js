import { registerFormatType, toggleFormat } from '@wordpress/rich-text';
import { RichTextToolbarButton } from '@wordpress/block-editor';

const formatName = 'soundbridge/pale-blue-highlight';
const highlightIcon = (
    <svg width="20" height="20" viewBox="0 0 20 20" aria-hidden="true">
        <path d="M5 4h2v5h6V4h2v12h-2v-5H7v5H5z" fill="currentColor" />
        <path d="M3 16h14v2H3z" fill="var(--blue-light)" />
    </svg>
);

if (!globalThis.soundbridgePaleBlueHighlightRegistered) {
    registerFormatType(formatName, {
        title: 'Blue highlight',
        tagName: 'span',
        className: 'sb-highlight-pale-blue',
        edit: ({ isActive, onChange, onFocus, value }) => (
            <RichTextToolbarButton
                name="unknown"
                icon={highlightIcon}
                title="Blue highlight"
                isActive={isActive}
                onClick={() => {
                    onChange(toggleFormat(value, { type: formatName }));
                    onFocus();
                }}
            />
        ),
    });

    globalThis.soundbridgePaleBlueHighlightRegistered = true;
}
