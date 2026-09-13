import { RichText, useBlockProps } from '@wordpress/block-editor';

export default function save({ attributes }) {
    const { background = 'pale-blue', eyebrow, heading, steps = [] } = attributes;
    const sectionClass = `sb-scholarship-process sb-block-bg alignfull${background !== 'white' ? ` sb-block-bg--${background}` : ''}`;
    return <section {...useBlockProps.save({ className: sectionClass })}><div className="sb-container">
        <header className="sb-scholarship-process__header"><RichText.Content tagName="p" className="sb-eyebrow" value={eyebrow} /><RichText.Content tagName="h2" value={heading} /></header>
        <div className="sb-scholarship-process__steps">{steps.map((step, index) => <article className="sb-scholarship-step" key={index}><span className="sb-scholarship-step__number">{index + 1}</span><RichText.Content tagName="h3" value={step.title} /><RichText.Content tagName="p" value={step.body} /></article>)}</div>
    </div></section>;
}
