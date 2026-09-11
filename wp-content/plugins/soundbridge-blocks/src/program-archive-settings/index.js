import apiFetch from '@wordpress/api-fetch';
import { RichText, URLInput } from '@wordpress/block-editor';
import { Button, Notice, Spinner } from '@wordpress/components';
import domReady from '@wordpress/dom-ready';
import { createRoot, useState } from '@wordpress/element';
import './style.scss';

const initialSettings = window.soundbridgeProgramArchiveSettings || {};
const richTextFormats = ['core/bold', 'core/italic'];

function EditableText({ tagName = 'p', field, settings, setSettings, className = '', placeholder = '' }) {
    return (
        <RichText
            tagName={tagName}
            className={className}
            value={settings[field] || ''}
            allowedFormats={richTextFormats}
            placeholder={placeholder}
            onChange={(value) => setSettings((current) => ({ ...current, [field]: value }))}
        />
    );
}

function LinkField({ label, field, settings, setSettings }) {
    return (
        <div className="sb-archive-editor__link-field">
            <strong>{label}</strong>
            <URLInput
                value={settings[field] || ''}
                onChange={(value) => setSettings((current) => ({ ...current, [field]: value }))}
            />
        </div>
    );
}

function ArchiveSettingsEditor() {
    const [settings, setSettings] = useState(initialSettings);
    const [isSaving, setIsSaving] = useState(false);
    const [notice, setNotice] = useState(null);

    const save = async () => {
        setIsSaving(true);
        setNotice(null);
        try {
            const saved = await apiFetch({ path: '/soundbridge/v1/program-archive', method: 'POST', data: settings });
            setSettings(saved);
            setNotice({ status: 'success', message: 'Program archive settings saved.' });
        } catch (error) {
            setNotice({ status: 'error', message: error.message || 'The settings could not be saved.' });
        } finally {
            setIsSaving(false);
        }
    };

    const updateImage = (image) => setSettings((current) => ({
        ...current,
        hero_image_id: image.id,
        hero_image_url: image.url,
    }));

    const openMediaLibrary = () => {
        const frame = window.wp.media({
            title: 'Choose archive hero image',
            button: { text: 'Use this image' },
            library: { type: 'image' },
            multiple: false,
        });
        frame.on('select', () => updateImage(frame.state().get('selection').first().toJSON()));
        frame.open();
    };

    return (
        <div className="sb-archive-editor">
            <header className="sb-archive-editor__toolbar">
                <div><h1>Program Archive Settings</h1><p>Edit text directly in the previews, then save your changes.</p></div>
                <Button variant="primary" onClick={save} disabled={isSaving}>{isSaving ? <><Spinner /> Saving…</> : 'Save Changes'}</Button>
            </header>

            {notice && <Notice status={notice.status} onRemove={() => setNotice(null)}>{notice.message}</Notice>}

            <div className="sb-archive-editor__layout">
                <main className="sb-archive-editor__canvas">
                    <section className="sb-archive-editor__section">
                        <div className="sb-archive-editor__section-label">Hero</div>
                        <div className="sb-archive-editor__hero-media-control">
                            <Button variant="secondary" onClick={openMediaLibrary}>
                                <span className="dashicons dashicons-format-image" aria-hidden="true" />
                                {settings.hero_image_url ? 'Replace hero image' : 'Choose hero image'}
                            </Button>
                        </div>
                        <div className="sb-archive-editor__hero" style={settings.hero_image_url ? { '--sb-admin-hero-image': `url(${settings.hero_image_url})` } : {}}>
                            <div className="sb-archive-editor__hero-content">
                                <EditableText field="hero_eyebrow" settings={settings} setSettings={setSettings} className="sb-eyebrow" placeholder="Hero eyebrow" />
                                <h2>
                                    <EditableText tagName="span" field="hero_heading" settings={settings} setSettings={setSettings} placeholder="Hero heading" />
                                    <EditableText tagName="em" field="hero_highlight" settings={settings} setSettings={setSettings} className="sb-highlight" placeholder="Highlighted heading" />
                                </h2>
                                <EditableText field="hero_description" settings={settings} setSettings={setSettings} className="sb-lead" placeholder="Hero description" />
                            </div>
                        </div>
                    </section>

                    <section className="sb-archive-editor__section">
                        <div className="sb-archive-editor__section-label">Callout</div>
                        <div className="sb-archive-editor__callout">
                            <div>
                                <EditableText field="callout_eyebrow" settings={settings} setSettings={setSettings} className="sb-eyebrow" placeholder="Callout eyebrow" />
                                <EditableText tagName="h2" field="callout_heading" settings={settings} setSettings={setSettings} placeholder="Callout heading" />
                                <EditableText field="callout_description" settings={settings} setSettings={setSettings} placeholder="Callout description" />
                            </div>
                            <div className="sb-archive-editor__buttons">
                                <EditableText tagName="span" field="callout_primary_label" settings={settings} setSettings={setSettings} className="sb-admin-btn sb-admin-btn--outline" placeholder="First button" />
                                <EditableText tagName="span" field="callout_second_label" settings={settings} setSettings={setSettings} className="sb-admin-btn" placeholder="Second button" />
                            </div>
                        </div>
                    </section>

                    <section className="sb-archive-editor__section">
                        <div className="sb-archive-editor__section-label">CTA</div>
                        <div className="sb-archive-editor__cta">
                            <div>
                                <EditableText field="cta_eyebrow" settings={settings} setSettings={setSettings} className="sb-eyebrow" placeholder="CTA eyebrow" />
                                <EditableText tagName="h2" field="cta_heading" settings={settings} setSettings={setSettings} placeholder="CTA heading" />
                                <EditableText field="cta_description" settings={settings} setSettings={setSettings} placeholder="CTA description" />
                            </div>
                            <EditableText tagName="span" field="cta_button_label" settings={settings} setSettings={setSettings} className="sb-admin-btn sb-admin-btn--light" placeholder="CTA button" />
                        </div>
                    </section>
                </main>

                <aside className="sb-archive-editor__sidebar">
                    <section>
                        <h2>Hero image</h2>
                        {settings.hero_image_url && <img src={settings.hero_image_url} alt="" />}
                        <div className="sb-archive-editor__media-actions">
                            <Button variant="secondary" onClick={openMediaLibrary}>{settings.hero_image_url ? 'Replace image' : 'Choose image'}</Button>
                            {settings.hero_image_url && <Button isDestructive variant="link" onClick={() => setSettings((current) => ({ ...current, hero_image_id: 0, hero_image_url: '' }))}>Remove</Button>}
                        </div>
                        <p className="description">The first Program image is used when this is empty.</p>
                    </section>
                    <section><h2>Callout links</h2><LinkField label="First button" field="callout_primary_url" settings={settings} setSettings={setSettings} /><LinkField label="Second button" field="callout_second_url" settings={settings} setSettings={setSettings} /></section>
                    <section><h2>CTA link</h2><LinkField label="Button destination" field="cta_button_url" settings={settings} setSettings={setSettings} /></section>
                </aside>
            </div>
        </div>
    );
}

domReady(() => {
    const container = document.getElementById('soundbridge-program-archive-settings');
    if (container) createRoot(container).render(<ArchiveSettingsEditor />);
});
