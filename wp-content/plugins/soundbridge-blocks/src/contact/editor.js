import { InspectorControls, RichText, URLInput } from '@wordpress/block-editor';
import { Button, Notice, PanelBody, SelectControl, Spinner, TextControl, TextareaControl } from '@wordpress/components';
import apiFetch from '@wordpress/api-fetch';
import { useEffect, useState } from '@wordpress/element';

const ContactIcon = ({ type }) => {
    const paths = {
        email: <><rect width="20" height="16" x="2" y="4" rx="2" /><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" /></>,
        website: <><circle cx="12" cy="12" r="10" /><path d="M2 12h20M12 2a15.3 15.3 0 0 1 0 20M12 2a15.3 15.3 0 0 0 0 20" /></>,
        venue: <><path d="M20 10c0 5-5.5 10.5-7.4 12.3a.83.83 0 0 1-1.2 0C9.5 20.5 4 15 4 10a8 8 0 1 1 16 0" /><circle cx="12" cy="10" r="3" /></>,
    };

    return <svg className="sb-contact__info-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">{paths[type]}</svg>;
};

export default function Edit({ attributes, setAttributes }) {
    const { background = 'white', eyebrow, heading, email, websiteLabel, websiteUrl, venue, responseText, fluentFormId = 0, submitLabel, quickLinks = [] } = attributes;
    const [forms, setForms] = useState([]);
    const [formsError, setFormsError] = useState('');
    const [isLoadingForms, setIsLoadingForms] = useState(true);
    const sectionClass = `sb-contact sb-block-bg alignfull${background !== 'white' ? ` sb-block-bg--${background}` : ''}`;
    const updateLink = (index, key, value) => setAttributes({ quickLinks: quickLinks.map((link, linkIndex) => linkIndex === index ? { ...link, [key]: value } : link) });
    const selectedForm = forms.find((form) => form.id === Number(fluentFormId));

    useEffect(() => {
        apiFetch({ path: '/soundbridge/v1/fluent-forms' }).then(setForms).catch((error) => setFormsError(error.message || 'Unable to load Fluent Forms.')).finally(() => setIsLoadingForms(false));
    }, []);

    return <>
        <InspectorControls>
            <PanelBody title="Contact settings">
                <SelectControl label="Background color" value={background} options={[{ label: 'White', value: 'white' }, { label: 'Pale blue', value: 'pale-blue' }]} onChange={(value) => setAttributes({ background: value })} />
                {isLoadingForms ? <div className="sb-contact__forms-loading"><Spinner /> Loading Fluent Forms…</div> : <SelectControl label="Fluent Form" help="Choose the form displayed by this block." value={String(fluentFormId)} options={[{ label: 'Use built-in contact form', value: '0' }, ...forms.map((form) => ({ label: form.title, value: String(form.id) }))]} onChange={(value) => setAttributes({ fluentFormId: Number(value) })} />}
                {formsError && <Notice status="warning" isDismissible={false}>{formsError} Make sure Fluent Forms is active.</Notice>}
                <TextControl label="Contact email" value={email} onChange={(value) => setAttributes({ email: value })} />
                <TextControl label="Website label" value={websiteLabel} onChange={(value) => setAttributes({ websiteLabel: value })} />
                <div><p className="sb-editor-field-label">Website link</p><URLInput value={websiteUrl} onChange={(value) => setAttributes({ websiteUrl: value })} /></div>
                <TextareaControl label="Primary venue" value={venue} onChange={(value) => setAttributes({ venue: value })} />
            </PanelBody>
            <PanelBody title="Quick links" initialOpen={false}>
                {quickLinks.map((link, index) => <div className="sb-contact__link-control" key={index}><TextControl label={`Link ${index + 1} label`} value={link.label} onChange={(value) => updateLink(index, 'label', value)} /><div><p className="sb-editor-field-label">Link destination</p><URLInput value={link.url} onChange={(value) => updateLink(index, 'url', value)} /></div><Button isDestructive variant="link" onClick={() => setAttributes({ quickLinks: quickLinks.filter((_, linkIndex) => linkIndex !== index) })}>Remove link</Button></div>)}
                <Button variant="secondary" onClick={() => setAttributes({ quickLinks: [...quickLinks, { label: '', url: '' }] })}>Add quick link</Button>
            </PanelBody>
        </InspectorControls>
        <section className={sectionClass}><div className="sb-container sb-contact__grid">
            <div><RichText tagName="p" className="sb-eyebrow" value={eyebrow} placeholder="Eyebrow" onChange={(value) => setAttributes({ eyebrow: value })} /><RichText tagName="h2" value={heading} placeholder="Heading" onChange={(value) => setAttributes({ heading: value })} />
                {fluentFormId ? <div className="sb-contact__form sb-contact__form--fluent sb-contact__fluent-preview"><span className="dashicons dashicons-feedback" aria-hidden="true" /><strong>{selectedForm?.title || `Fluent Form #${fluentFormId}`}</strong><p>The selected Fluent Form will render here on the website.</p></div> : <div className="sb-contact__form" aria-label="Contact form preview"><div className="sb-contact__row"><label>Full Name *<input disabled placeholder="Your name" /></label><label>Email Address *<input disabled placeholder="you@example.com" /></label></div><label>Subject *<select disabled><option>Select a topic…</option></select></label><label>Message *<textarea disabled rows="6" placeholder="How can we help you?" /></label><RichText tagName="span" className="sb-btn" value={submitLabel} placeholder="Submit label" onChange={(value) => setAttributes({ submitLabel: value })} /></div>}
            </div>
            <aside className="sb-contact__sidebar"><div className="sb-contact__info"><h3>Contact Information</h3><div className="sb-contact__info-row"><ContactIcon type="email" /><div><strong>Email</strong><span>{email}</span></div></div><div className="sb-contact__info-row"><ContactIcon type="website" /><div><strong>Website</strong><span>{websiteLabel}</span></div></div><div className="sb-contact__info-row"><ContactIcon type="venue" /><div><strong>Primary Venue</strong><span>{venue}</span></div></div></div><div className="sb-contact__response"><h3>Response Time</h3><RichText tagName="p" value={responseText} onChange={(value) => setAttributes({ responseText: value })} /></div><div className="sb-contact__quick-links"><h3>Quick Links</h3>{quickLinks.map((link, index) => <span key={index}>→ {link.label}</span>)}</div></aside>
        </div></section>
    </>;
}
