import { RichText } from '@wordpress/block-editor';

export default function save({ attributes }) {
    const { background = 'pale-blue', pathways = [] } = attributes;
    return <section className={`sb-involvement sb-block-bg alignfull sb-block-bg--${background}`} data-sb-involvement><div className="sb-container">
        <div className="sb-involvement__tabs" role="tablist" aria-label="Ways to get involved">{pathways.map((item, index) => <button className={index === 0 ? 'is-active' : ''} type="button" role="tab" aria-selected={index === 0} aria-controls={`sb-pathway-${index}`} data-sb-pathway-tab={index} key={index}><span aria-hidden="true">{item.icon}</span>{item.label}</button>)}</div>
        <div className="sb-involvement__panels">{pathways.map((item, index) => <article className={`sb-involvement__panel${index === 0 ? ' is-active' : ''}`} id={`sb-pathway-${index}`} role="tabpanel" hidden={index !== 0} data-sb-pathway-panel={index} key={index}><div><span className="sb-involvement__icon" aria-hidden="true">{item.icon}</span><RichText.Content tagName="p" className="sb-eyebrow" value={item.label || ''} /><RichText.Content tagName="h2" value={item.title || ''} /><RichText.Content tagName="p" className="sb-involvement__body" value={item.body || ''} />{item.buttonLabel && <a className="sb-btn" href={item.buttonUrl}>{item.buttonLabel} →</a>}</div><div className="sb-involvement__details"><RichText.Content tagName="h3" value={item.listHeading || ''} /><ul>{(item.items || []).map((detail, detailIndex) => <li key={detailIndex}>{detail}</li>)}</ul></div></article>)}</div>
    </div></section>;
}
