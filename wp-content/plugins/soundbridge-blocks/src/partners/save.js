import { RichText } from '@wordpress/block-editor';

export default function save({ attributes }) {
    const { background = 'white', eyebrow, heading, partners = [], showLink, linkIntro, linkLabel, linkUrl } = attributes;
    const sectionClass = `sb-partners sb-block-bg alignfull${background !== 'white' ? ` sb-block-bg--${background}` : ''}`;
    return <section className={sectionClass}><div className="sb-container">
        <header className="sb-partners__header"><RichText.Content tagName="p" className="sb-eyebrow" value={eyebrow} /><RichText.Content tagName="h2" value={heading} /></header>
        <div className="sb-partners__list">{partners.map((partner, index) => <RichText.Content tagName="span" className="sb-partners__partner" value={partner || ''} key={index} />)}</div>
        {showLink && <p className="sb-partners__cta"><RichText.Content tagName="span" value={linkIntro} /> <a className="sb-partners__link" href={linkUrl}>{linkLabel}</a></p>}
    </div></section>;
}
