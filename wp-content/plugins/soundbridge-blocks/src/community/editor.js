import { InspectorControls, RichText } from '@wordpress/block-editor';
import { Button, PanelBody, TextControl, ToggleControl } from '@wordpress/components';

export default function Edit({ attributes, setAttributes }) {
    const { peopleEyebrow, peopleHeading, people = [], partnersEyebrow, partnersHeading, partners = [], showPartnerLink, partnerLinkIntro, partnerLinkLabel, partnerLinkUrl } = attributes;
    const updatePerson = (index, key, value) => setAttributes({ people: people.map((person, personIndex) => personIndex === index ? { ...person, [key]: value } : person) });
    const move = (collection, index, direction) => {
        const destination = index + direction;
        if (destination < 0 || destination >= collection.length) return collection;
        const next = [...collection];
        [next[index], next[destination]] = [next[destination], next[index]];
        return next;
    };

    return (
        <>
            <InspectorControls>
                <PanelBody title="Partner link">
                    <ToggleControl label="Show partner link" checked={showPartnerLink} onChange={(value) => setAttributes({ showPartnerLink: value })} />
                    {showPartnerLink && <TextControl label="Link URL" value={partnerLinkUrl} onChange={(value) => setAttributes({ partnerLinkUrl: value })} />}
                </PanelBody>
            </InspectorControls>
            <section className="sb-community alignfull">
                <div className="sb-community__people sb-section">
                    <div className="sb-container">
                        <header className="sb-community__header">
                            <RichText tagName="p" className="sb-eyebrow" value={peopleEyebrow} placeholder="Eyebrow" onChange={(value) => setAttributes({ peopleEyebrow: value })} />
                            <RichText tagName="h2" value={peopleHeading} placeholder="People heading" onChange={(value) => setAttributes({ peopleHeading: value })} />
                        </header>
                        <div className="sb-community__people-grid">
                            {people.map((person, index) => (
                                <article className="sb-person" key={index}>
                                    <div className="sb-community__controls">
                                        <Button label="Move person left" disabled={index === 0} onClick={() => setAttributes({ people: move(people, index, -1) })} size="small"><span className="dashicons dashicons-arrow-up-alt2" aria-hidden="true" /></Button>
                                        <Button label="Move person right" disabled={index === people.length - 1} onClick={() => setAttributes({ people: move(people, index, 1) })} size="small"><span className="dashicons dashicons-arrow-down-alt2" aria-hidden="true" /></Button>
                                        <Button label="Remove person" isDestructive onClick={() => setAttributes({ people: people.filter((_, personIndex) => personIndex !== index) })} size="small"><span className="dashicons dashicons-trash" aria-hidden="true" /></Button>
                                    </div>
                                    <span className="sb-person__avatar" aria-hidden="true">{person.name?.trim().charAt(0) || '?'}</span>
                                    <RichText tagName="h3" value={person.name} placeholder="Name" onChange={(value) => updatePerson(index, 'name', value)} />
                                    <RichText tagName="p" className="sb-person__role" value={person.role} placeholder="Role" onChange={(value) => updatePerson(index, 'role', value)} />
                                    <RichText tagName="p" className="sb-person__bio" value={person.bio} placeholder="Biography" onChange={(value) => updatePerson(index, 'bio', value)} />
                                </article>
                            ))}
                        </div>
                        <Button className="sb-community__add" variant="secondary" onClick={() => setAttributes({ people: [...people, { name: '', role: '', bio: '' }] })}><span className="dashicons dashicons-plus" aria-hidden="true" />Add person</Button>
                    </div>
                </div>
                <div className="sb-community__partners sb-section">
                    <div className="sb-container">
                        <header className="sb-community__header">
                            <RichText tagName="p" className="sb-eyebrow" value={partnersEyebrow} placeholder="Eyebrow" onChange={(value) => setAttributes({ partnersEyebrow: value })} />
                            <RichText tagName="h2" value={partnersHeading} placeholder="Partners heading" onChange={(value) => setAttributes({ partnersHeading: value })} />
                        </header>
                        <div className="sb-community__partner-list">
                            {partners.map((partner, index) => (
                                <div className="sb-community__partner" key={index}>
                                    <RichText tagName="span" value={partner} allowedFormats={[]} placeholder="Partner name" onChange={(value) => setAttributes({ partners: partners.map((name, partnerIndex) => partnerIndex === index ? value : name) })} />
                                    <Button label="Move partner left" disabled={index === 0} onClick={() => setAttributes({ partners: move(partners, index, -1) })} size="small"><span className="dashicons dashicons-arrow-up-alt2" aria-hidden="true" /></Button>
                                    <Button label="Move partner right" disabled={index === partners.length - 1} onClick={() => setAttributes({ partners: move(partners, index, 1) })} size="small"><span className="dashicons dashicons-arrow-down-alt2" aria-hidden="true" /></Button>
                                    <Button label="Remove partner" isDestructive onClick={() => setAttributes({ partners: partners.filter((_, partnerIndex) => partnerIndex !== index) })} size="small"><span className="dashicons dashicons-trash" aria-hidden="true" /></Button>
                                </div>
                            ))}
                        </div>
                        <Button className="sb-community__add" variant="secondary" onClick={() => setAttributes({ partners: [...partners, ''] })}><span className="dashicons dashicons-plus" aria-hidden="true" />Add partner</Button>
                        {showPartnerLink && <p className="sb-community__partner-cta"><RichText tagName="span" value={partnerLinkIntro} placeholder="Link introduction" onChange={(value) => setAttributes({ partnerLinkIntro: value })} /> <RichText tagName="span" className="sb-community__partner-link" value={partnerLinkLabel} placeholder="Link label" onChange={(value) => setAttributes({ partnerLinkLabel: value })} /></p>}
                    </div>
                </div>
            </section>
        </>
    );
}
