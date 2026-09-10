import { InspectorControls, RichText } from '@wordpress/block-editor';
import { Button, PanelBody, SelectControl, TextControl, TextareaControl } from '@wordpress/components';

export default function Edit({ attributes, setAttributes }) {
    const { background = 'pale-blue', pathways = [] } = attributes;
    const update = (index, values) => setAttributes({ pathways: pathways.map((item, itemIndex) => itemIndex === index ? { ...item, ...values } : item) });
    return <>
        <InspectorControls><PanelBody title="Pathways settings"><SelectControl label="Background color" value={background} options={[{ label: 'White', value: 'white' }, { label: 'Pale blue', value: 'pale-blue' }, { label: 'Dark blue', value: 'dark-blue' }]} onChange={(value) => setAttributes({ background: value })} /></PanelBody></InspectorControls>
        <section className={`sb-involvement sb-block-bg alignfull sb-block-bg--${background}`}><div className="sb-container">
            <div className="sb-involvement__tabs">{pathways.map((item, index) => <span className={index === 0 ? 'is-active' : ''} key={index}>{item.icon} {item.label}</span>)}</div>
            <div className="sb-involvement__editor-list">{pathways.map((item, index) => <article className="sb-involvement__panel is-active" key={index}><div><TextControl label="Icon or emoji" value={item.icon} onChange={(value) => update(index, { icon: value })} /><RichText tagName="p" className="sb-eyebrow" value={item.label} placeholder="Label" onChange={(value) => update(index, { label: value })} /><RichText tagName="h2" value={item.title} placeholder="Title" onChange={(value) => update(index, { title: value })} /><RichText tagName="p" value={item.body} placeholder="Description" onChange={(value) => update(index, { body: value })} /><RichText tagName="span" className="sb-btn" value={item.buttonLabel} placeholder="Button label" onChange={(value) => update(index, { buttonLabel: value })} /><TextControl label="Button URL" value={item.buttonUrl} onChange={(value) => update(index, { buttonUrl: value })} /></div><div className="sb-involvement__details"><RichText tagName="h3" value={item.listHeading} placeholder="List heading" onChange={(value) => update(index, { listHeading: value })} /><TextareaControl label="List items (one per line)" value={(item.items || []).join('\n')} onChange={(value) => update(index, { items: value.split('\n').filter(Boolean) })} /></div><Button isDestructive variant="link" onClick={() => setAttributes({ pathways: pathways.filter((_, itemIndex) => itemIndex !== index) })}>Remove pathway</Button></article>)}</div>
            <Button variant="secondary" onClick={() => setAttributes({ pathways: [...pathways, { icon: '♪', label: '', title: '', body: '', buttonLabel: '', buttonUrl: '', listHeading: '', items: [] }] })}>Add pathway</Button>
        </div></section>
    </>;
}
