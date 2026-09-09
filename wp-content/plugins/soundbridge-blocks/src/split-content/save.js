import { RichText } from '@wordpress/block-editor';

export default function save({ attributes }) {
    const { eyebrow, heading, text, imageUrl, imageAlt, reverse, background } = attributes;
    const sectionClass = `sb-section alignfull${background === 'blue' ? ' sb-section--pale' : ''}`;
    const splitClass = `sb-container sb-split${reverse ? ' sb-split--reverse' : ''}`;

    return (
        <section className={sectionClass}>
            <div className={splitClass}>
                <div>
                    {eyebrow && <p className="sb-eyebrow">{eyebrow}</p>}
                    <RichText.Content tagName="h2" value={heading} />
                    <RichText.Content tagName="p" className="sb-copy" value={text} />
                </div>
                {imageUrl && <img src={imageUrl} alt={imageAlt} />}
            </div>
        </section>
    );
}
