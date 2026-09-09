import { RichText } from '@wordpress/block-editor';

export default function save({ attributes }) {
    const { items = [] } = attributes;

    return (
        <section className="sb-stats alignfull">
            <div className="sb-container sb-stats__grid">
                {items.map((item, index) => (
                    <div className="sb-stats__item" key={index}>
                        <RichText.Content tagName="p" className="sb-stats__stat" value={item.value || ''} />
                        <RichText.Content tagName="p" className="sb-stats__label" value={item.label || ''} />
                    </div>
                ))}
            </div>
        </section>
    );
}
