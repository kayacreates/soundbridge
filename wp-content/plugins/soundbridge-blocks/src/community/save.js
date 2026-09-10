import { RichText } from '@wordpress/block-editor';

export default function save({ attributes }) {
    const { peopleEyebrow, peopleHeading, people = [], partnersEyebrow, partnersHeading, partners = [], showPartnerLink, partnerLinkIntro, partnerLinkLabel, partnerLinkUrl } = attributes;
    return (
        <section className="sb-community alignfull">
            <div className="sb-community__people sb-section"><div className="sb-container">
                <header className="sb-community__header"><RichText.Content tagName="p" className="sb-eyebrow" value={peopleEyebrow} /><RichText.Content tagName="h2" value={peopleHeading} /></header>
                <div className="sb-community__people-grid">{people.map((person, index) => <article className="sb-person" key={index}><span className="sb-person__avatar" aria-hidden="true">{person.name?.trim().charAt(0) || '?'}</span><RichText.Content tagName="h3" value={person.name || ''} /><RichText.Content tagName="p" className="sb-person__role" value={person.role || ''} /><RichText.Content tagName="p" className="sb-person__bio" value={person.bio || ''} /></article>)}</div>
            </div></div>
            <div className="sb-community__partners sb-section"><div className="sb-container">
                <header className="sb-community__header"><RichText.Content tagName="p" className="sb-eyebrow" value={partnersEyebrow} /><RichText.Content tagName="h2" value={partnersHeading} /></header>
                <div className="sb-community__partner-list">{partners.map((partner, index) => <RichText.Content tagName="span" className="sb-community__partner" value={partner || ''} key={index} />)}</div>
                {showPartnerLink && <p className="sb-community__partner-cta"><RichText.Content tagName="span" value={partnerLinkIntro} /> <a className="sb-community__partner-link" href={partnerLinkUrl}>{partnerLinkLabel}</a></p>}
            </div></div>
        </section>
    );
}
