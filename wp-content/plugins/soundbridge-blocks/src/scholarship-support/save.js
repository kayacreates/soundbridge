import { RichText, useBlockProps } from '@wordpress/block-editor';

export default function save({ attributes }) {
    const { background = 'pale-blue', eyebrow, heading, text, showButton, buttonLabel, buttonUrl, gifts = [] } = attributes;
    const sectionClass = `sb-scholarship-support sb-block-bg alignfull${background !== 'white' ? ` sb-block-bg--${background}` : ''}`;
    return <section {...useBlockProps.save({ className: sectionClass })}><div className="sb-container sb-scholarship-support__grid"><div><RichText.Content tagName="p" className="sb-eyebrow" value={eyebrow} /><RichText.Content tagName="h2" value={heading} /><RichText.Content tagName="p" className="sb-scholarship-support__copy" value={text} />{showButton && buttonLabel && <a className="sb-btn" href={buttonUrl}><RichText.Content tagName="span" value={buttonLabel} /></a>}</div><div className="sb-scholarship-gifts">{gifts.map((gift, index) => <div className="sb-scholarship-gift" key={index}><RichText.Content tagName="strong" value={gift.amount} /><RichText.Content tagName="span" value={gift.description} /></div>)}</div></div></section>;
}
