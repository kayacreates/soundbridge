import { RichText } from '@wordpress/block-editor';

export default function save({ attributes }) {
    const { showButton, heading, text, buttonLabel, buttonUrl } = attributes;

    return (
        <section className="sb-cta alignfull">
            <div className="sb-container">
                <RichText.Content tagName="h2" value={heading} />
                <RichText.Content tagName="p" value={text} />
                {showButton && (
                    <a className="sb-btn sb-btn--light" href={buttonUrl}>
                        {buttonLabel}
                    </a>
                )}
            </div>
        </section>
    );
}
