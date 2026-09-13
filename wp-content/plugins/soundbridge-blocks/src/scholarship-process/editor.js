import { InspectorControls, RichText, useBlockProps } from '@wordpress/block-editor';
import { Button, PanelBody, SelectControl } from '@wordpress/components';

const move = (items, index, direction) => {
    const destination = index + direction;
    if (destination < 0 || destination >= items.length) return items;
    const next = [...items];
    [next[index], next[destination]] = [next[destination], next[index]];
    return next;
};

export default function Edit({ attributes, setAttributes }) {
    const { background = 'pale-blue', eyebrow, heading, steps = [] } = attributes;
    const update = (index, values) => setAttributes({ steps: steps.map((step, stepIndex) => stepIndex === index ? { ...step, ...values } : step) });
    const sectionClass = `sb-scholarship-process sb-block-bg alignfull${background !== 'white' ? ` sb-block-bg--${background}` : ''}`;
    const blockProps = useBlockProps({ className: sectionClass });
    return <>
        <InspectorControls><PanelBody title="Scholarship process settings"><SelectControl label="Background color" value={background} options={[{ label: 'White', value: 'white' }, { label: 'Pale blue', value: 'pale-blue' }, { label: 'Dark blue', value: 'dark-blue' }]} onChange={(value) => setAttributes({ background: value })} /></PanelBody></InspectorControls>
        <section {...blockProps}><div className="sb-container">
            <header className="sb-scholarship-process__header"><RichText tagName="p" className="sb-eyebrow" value={eyebrow} placeholder="Eyebrow" onChange={(value) => setAttributes({ eyebrow: value })} /><RichText tagName="h2" value={heading} allowedFormats={['core/bold', 'core/italic', 'soundbridge/pale-blue-highlight']} placeholder="Process heading" onChange={(value) => setAttributes({ heading: value })} /></header>
            <div className="sb-scholarship-process__steps">{steps.map((step, index) => <article className="sb-scholarship-step" key={index}>
                <div className="sb-scholarship-step__controls"><Button label="Move step left" disabled={index === 0} onClick={() => setAttributes({ steps: move(steps, index, -1) })} size="small"><span className="dashicons dashicons-arrow-left-alt2" /></Button><Button label="Move step right" disabled={index === steps.length - 1} onClick={() => setAttributes({ steps: move(steps, index, 1) })} size="small"><span className="dashicons dashicons-arrow-right-alt2" /></Button><Button label="Remove step" isDestructive onClick={() => setAttributes({ steps: steps.filter((_, stepIndex) => stepIndex !== index) })} size="small"><span className="dashicons dashicons-trash" /></Button></div>
                <span className="sb-scholarship-step__number">{index + 1}</span><RichText tagName="h3" value={step.title} placeholder="Step title" onChange={(value) => update(index, { title: value })} /><RichText tagName="p" value={step.body} placeholder="Step description" onChange={(value) => update(index, { body: value })} />
            </article>)}</div>
            <Button className="sb-scholarship-process__add" variant="secondary" onClick={() => setAttributes({ steps: [...steps, { title: '', body: '' }] })}><span className="dashicons dashicons-plus" />Add step</Button>
        </div></section>
    </>;
}
