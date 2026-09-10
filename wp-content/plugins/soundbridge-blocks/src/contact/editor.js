import { InspectorControls, RichText } from '@wordpress/block-editor';
import { Button, PanelBody, SelectControl, TextControl, TextareaControl } from '@wordpress/components';

export default function Edit({ attributes, setAttributes }) {
    const { background = 'white', eyebrow, heading, email, websiteLabel, websiteUrl, venue, responseText, submitLabel, quickLinks = [] } = attributes;
    const sectionClass = `sb-contact sb-section alignfull${background !== 'white' ? ` sb-block-bg--${background}` : ''}`;
    const updateLink = (index, key, value) => setAttributes({ quickLinks: quickLinks.map((link, linkIndex) => linkIndex === index ? { ...link, [key]: value } : link) });

    return <>
        <InspectorControls>
            <PanelBody title="Contact settings">
                <SelectControl label="Background color" value={background} options={[{ label: 'White', value: 'white' }, { label: 'Pale blue', value: 'pale-blue' }, { label: 'Dark blue', value: 'dark-blue' }]} onChange={(value) => setAttributes({ background: value })} />
                <TextControl label="Contact email" value={email} onChange={(value) => setAttributes({ email: value })} />
                <TextControl label="Website label" value={websiteLabel} onChange={(value) => setAttributes({ websiteLabel: value })} />
                <TextControl label="Website URL" value={websiteUrl} onChange={(value) => setAttributes({ websiteUrl: value })} />
                <TextareaControl label="Primary venue" value={venue} onChange={(value) => setAttributes({ venue: value })} />
            </PanelBody>
            <PanelBody title="Quick links" initialOpen={false}>
                {quickLinks.map((link, index) => <div className="sb-contact__link-control" key={index}><TextControl label={`Link ${index + 1} label`} value={link.label} onChange={(value) => updateLink(index, 'label', value)} /><TextControl label="URL" value={link.url} onChange={(value) => updateLink(index, 'url', value)} /><Button isDestructive variant="link" onClick={() => setAttributes({ quickLinks: quickLinks.filter((_, linkIndex) => linkIndex !== index) })}>Remove link</Button></div>)}
                <Button variant="secondary" onClick={() => setAttributes({ quickLinks: [...quickLinks, { label: '', url: '' }] })}>Add quick link</Button>
            </PanelBody>
        </InspectorControls>
        <section className={sectionClass}><div className="sb-container sb-contact__grid">
            <div><RichText tagName="p" className="sb-eyebrow" value={eyebrow} placeholder="Eyebrow" onChange={(value) => setAttributes({ eyebrow: value })} /><RichText tagName="h2" value={heading} placeholder="Heading" onChange={(value) => setAttributes({ heading: value })} />
                <div className="sb-contact__form" aria-label="Contact form preview"><div className="sb-contact__row"><label>Full Name *<input disabled placeholder="Your name" /></label><label>Email Address *<input disabled placeholder="you@example.com" /></label></div><label>Subject *<select disabled><option>Select a topic…</option></select></label><label>Message *<textarea disabled rows="6" placeholder="How can we help you?" /></label><RichText tagName="span" className="sb-btn" value={submitLabel} placeholder="Submit label" onChange={(value) => setAttributes({ submitLabel: value })} /></div>
            </div>
            <aside className="sb-contact__sidebar"><div className="sb-contact__info"><h3>Contact Information</h3><div><strong>Email</strong><span>{email}</span></div><div><strong>Website</strong><span>{websiteLabel}</span></div><div><strong>Primary Venue</strong><span>{venue}</span></div></div><div className="sb-contact__response"><h3>Response Time</h3><RichText tagName="p" value={responseText} onChange={(value) => setAttributes({ responseText: value })} /></div><div className="sb-contact__quick-links"><h3>Quick Links</h3>{quickLinks.map((link, index) => <span key={index}>→ {link.label}</span>)}</div></aside>
        </div></section>
    </>;
}
