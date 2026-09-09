import { RichText } from '@wordpress/block-editor';

export default function save({ attributes }) {
    const { items = [] } = attributes;

    return (
        <section className="sb-stats alignfull">
            <div className="sb-container sb-stats__grid">
                {items.map((item, index) => (
                    <div key={index}>
                        <RichText.Content tagName="strong" value={item.value || ''} />
                        <RichText.Content tagName="span" value={item.label || ''} />
                    </div>
                ))}
            </div>
        </section>
    );
}
