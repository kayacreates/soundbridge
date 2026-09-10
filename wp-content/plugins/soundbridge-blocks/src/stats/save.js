import { RichText } from '@wordpress/block-editor';

export default function save({ attributes }) {
    const { background = 'dark-blue', items = [] } = attributes;
    const sectionClass = `sb-stats alignfull sb-block-bg--${background}`;

    return (
        <section className={sectionClass}>
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
