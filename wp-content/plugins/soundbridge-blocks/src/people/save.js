import { RichText } from '@wordpress/block-editor';

export default function save({ attributes }) {
    const { background = 'pale-blue', eyebrow, heading, columns = 4, avatarStyle = 'letter', people = [] } = attributes;
    const sectionClass = `sb-people sb-block-bg alignfull sb-block-bg--${background}`;
    return (
        <section id={attributes.anchor || undefined} className={sectionClass}>
            <div className="sb-container">
                <header className="sb-people__header"><RichText.Content tagName="p" className="sb-eyebrow" value={eyebrow} /><RichText.Content tagName="h2" value={heading} /></header>
                <div className={`sb-people__grid sb-people__grid--${columns}`}>
                    {people.map((person, index) => <article className="sb-person" key={index}>
                        {avatarStyle === 'image' && person.imageUrl ? <div className="sb-person__portrait"><img src={person.imageUrl} alt={person.imageAlt || ''} loading="lazy" /></div> : <span className="sb-person__avatar" aria-hidden="true">{person.name?.trim().charAt(0) || '?'}</span>}
                        <RichText.Content tagName="h3" value={person.name || ''} />
                        <RichText.Content tagName="p" className="sb-person__role" value={person.role || ''} />
                        <RichText.Content tagName="p" className="sb-person__bio" value={person.bio || ''} />
                    </article>)}
                </div>
            </div>
        </section>
    );
}
