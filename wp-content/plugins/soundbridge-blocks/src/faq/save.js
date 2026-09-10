import { RichText } from '@wordpress/block-editor';

export default function save({ attributes }) {
    const { background = 'white', eyebrow, heading, items = [] } = attributes;
    const sectionClass = `sb-block-bg alignfull${background !== 'white' ? ` sb-block-bg--${background}` : ''}`;

    return (
        <section className={sectionClass}>
            <div className="sb-container sb-narrow">
                <p className="sb-eyebrow">{eyebrow}</p>
                <RichText.Content tagName="h2" value={heading} />
                <div className="sb-faq">
                    {items.map((item, index) => (
                        <details key={index}>
                            <summary><RichText.Content tagName="span" value={item.q || ''} /></summary>
                            <div><RichText.Content tagName="p" value={item.a || ''} /></div>
                        </details>
                    ))}
                </div>
            </div>
        </section>
    );
}
