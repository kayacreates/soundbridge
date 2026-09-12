import { InspectorControls, RichText, URLInput, useBlockProps } from '@wordpress/block-editor';
import { Button, PanelBody, SelectControl, ToggleControl } from '@wordpress/components';

const moveItem = (items, index, direction) => {
    const destination = index + direction;
    if (destination < 0 || destination >= items.length) return items;
    const next = [...items];
    [next[index], next[destination]] = [next[destination], next[index]];
    return next;
};

export default function Edit({ attributes, setAttributes }) {
    const { background = 'white', eyebrow, heading, partners = [], showLink, linkIntro, linkLabel, linkUrl } = attributes;
    const sectionClass = `sb-partners sb-block-bg alignfull${background !== 'white' ? ` sb-block-bg--${background}` : ''}`;
    return <>
        <InspectorControls><PanelBody title="Partners settings"><SelectControl label="Background color" value={background} options={[{ label: 'White', value: 'white' }, { label: 'Pale blue', value: 'pale-blue' }, { label: 'Dark blue', value: 'dark-blue' }]} onChange={(value) => setAttributes({ background: value })} /><ToggleControl label="Show partner link" checked={showLink} onChange={(value) => setAttributes({ showLink: value })} />{showLink && <div><p className="sb-editor-field-label">Partner link</p><URLInput value={linkUrl} onChange={(value) => setAttributes({ linkUrl: value })} /></div>}</PanelBody></InspectorControls>
        <section {...useBlockProps({ className: sectionClass })}><div className="sb-container">
            <header className="sb-partners__header"><RichText tagName="p" className="sb-eyebrow" value={eyebrow} placeholder="Eyebrow" onChange={(value) => setAttributes({ eyebrow: value })} /><RichText tagName="h2" value={heading} placeholder="Partners heading" onChange={(value) => setAttributes({ heading: value })} /></header>
            <div className="sb-partners__list">{partners.map((partner, index) => <div className="sb-partners__partner" key={index}><RichText tagName="span" value={partner} allowedFormats={[]} placeholder="Partner name" onChange={(value) => setAttributes({ partners: partners.map((name, partnerIndex) => partnerIndex === index ? value : name) })} /><Button label="Move partner left" disabled={index === 0} onClick={() => setAttributes({ partners: moveItem(partners, index, -1) })} size="small"><span className="dashicons dashicons-arrow-up-alt2" aria-hidden="true" /></Button><Button label="Move partner right" disabled={index === partners.length - 1} onClick={() => setAttributes({ partners: moveItem(partners, index, 1) })} size="small"><span className="dashicons dashicons-arrow-down-alt2" aria-hidden="true" /></Button><Button label="Remove partner" isDestructive onClick={() => setAttributes({ partners: partners.filter((_, partnerIndex) => partnerIndex !== index) })} size="small"><span className="dashicons dashicons-trash" aria-hidden="true" /></Button></div>)}</div>
            <Button className="sb-partners__add" variant="secondary" onClick={() => setAttributes({ partners: [...partners, ''] })}><span className="dashicons dashicons-plus" aria-hidden="true" />Add partner</Button>
            {showLink && <p className="sb-partners__cta"><RichText tagName="span" value={linkIntro} placeholder="Link introduction" onChange={(value) => setAttributes({ linkIntro: value })} /> <RichText tagName="span" className="sb-partners__link" value={linkLabel} placeholder="Link label" onChange={(value) => setAttributes({ linkLabel: value })} /></p>}
        </div></section>
    </>;
}
