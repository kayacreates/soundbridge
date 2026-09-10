import { InspectorControls, MediaUpload, MediaUploadCheck, RichText } from '@wordpress/block-editor';
import { Button, PanelBody, SelectControl, TextControl } from '@wordpress/components';

const moveItem = (items, index, direction) => {
    const destination = index + direction;
    if (destination < 0 || destination >= items.length) return items;
    const next = [...items];
    [next[index], next[destination]] = [next[destination], next[index]];
    return next;
};

export default function Edit({ attributes, setAttributes }) {
    const { background = 'pale-blue', eyebrow, heading, columns = 4, avatarStyle = 'letter', people = [] } = attributes;
    const updatePerson = (index, values) => setAttributes({ people: people.map((person, personIndex) => personIndex === index ? { ...person, ...values } : person) });
    const gridClass = `sb-people__grid sb-people__grid--${columns}`;
    const sectionClass = `sb-people sb-block-bg alignfull${background !== 'pale-blue' ? ` sb-block-bg--${background}` : ''}`;

    return (
        <>
            <InspectorControls>
                <PanelBody title="People settings">
                    <SelectControl label="Background color" value={background} options={[{ label: 'White', value: 'white' }, { label: 'Pale blue', value: 'pale-blue' }, { label: 'Dark blue', value: 'dark-blue' }]} onChange={(value) => setAttributes({ background: value })} />
                    <SelectControl label="Columns" value={String(columns)} options={[{ label: '2 columns', value: '2' }, { label: '3 columns', value: '3' }, { label: '4 columns', value: '4' }]} onChange={(value) => setAttributes({ columns: Number(value) })} />
                    <SelectControl label="Person display" value={avatarStyle} options={[{ label: 'Initial letter', value: 'letter' }, { label: 'Profile image', value: 'image' }]} onChange={(value) => setAttributes({ avatarStyle: value })} />
                </PanelBody>
            </InspectorControls>
            <section className={sectionClass}>
                <div className="sb-container">
                    <header className="sb-people__header">
                        <RichText tagName="p" className="sb-eyebrow" value={eyebrow} placeholder="Eyebrow" onChange={(value) => setAttributes({ eyebrow: value })} />
                        <RichText tagName="h2" value={heading} placeholder="People heading" onChange={(value) => setAttributes({ heading: value })} />
                    </header>
                    <div className={gridClass}>
                        {people.map((person, index) => (
                            <article className="sb-person" key={index}>
                                <div className="sb-people__controls">
                                    <Button label="Move person left" disabled={index === 0} onClick={() => setAttributes({ people: moveItem(people, index, -1) })} size="small"><span className="dashicons dashicons-arrow-up-alt2" aria-hidden="true" /></Button>
                                    <Button label="Move person right" disabled={index === people.length - 1} onClick={() => setAttributes({ people: moveItem(people, index, 1) })} size="small"><span className="dashicons dashicons-arrow-down-alt2" aria-hidden="true" /></Button>
                                    <Button label="Remove person" isDestructive onClick={() => setAttributes({ people: people.filter((_, personIndex) => personIndex !== index) })} size="small"><span className="dashicons dashicons-trash" aria-hidden="true" /></Button>
                                </div>
                                {avatarStyle === 'image' ? (
                                    <div className="sb-person__portrait">
                                        {person.imageUrl ? <img src={person.imageUrl} alt={person.imageAlt || ''} /> : null}
                                        <div className="sb-person__image-actions">
                                            <MediaUploadCheck><MediaUpload allowedTypes={['image']} value={person.imageId || 0} onSelect={(media) => updatePerson(index, { imageId: media.id, imageUrl: media.url, imageAlt: media.alt || media.title || '' })} render={({ open }) => <Button label={person.imageUrl ? 'Replace profile image' : 'Choose profile image'} onClick={open}><span className="dashicons dashicons-format-image" aria-hidden="true" /></Button>} /></MediaUploadCheck>
                                            {person.imageUrl && <Button label="Remove profile image" isDestructive onClick={() => updatePerson(index, { imageId: 0, imageUrl: '', imageAlt: '' })}><span className="dashicons dashicons-trash" aria-hidden="true" /></Button>}
                                        </div>
                                    </div>
                                ) : <span className="sb-person__avatar" aria-hidden="true">{person.name?.trim().charAt(0) || '?'}</span>}
                                {avatarStyle === 'image' && <TextControl label="Image alt text" value={person.imageAlt || ''} onChange={(value) => updatePerson(index, { imageAlt: value })} />}
                                <RichText tagName="h3" value={person.name} placeholder="Name" onChange={(value) => updatePerson(index, { name: value })} />
                                <RichText tagName="p" className="sb-person__role" value={person.role} placeholder="Role" onChange={(value) => updatePerson(index, { role: value })} />
                                <RichText tagName="p" className="sb-person__bio" value={person.bio} placeholder="Biography" onChange={(value) => updatePerson(index, { bio: value })} />
                            </article>
                        ))}
                    </div>
                    <Button className="sb-people__add" variant="secondary" onClick={() => setAttributes({ people: [...people, { name: '', role: '', bio: '', imageId: 0, imageUrl: '', imageAlt: '' }] })}><span className="dashicons dashicons-plus" aria-hidden="true" />Add person</Button>
                </div>
            </section>
        </>
    );
}
