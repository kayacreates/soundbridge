import { RichText } from '@wordpress/block-editor';

export default function save({ attributes }) {
    const { background = 'dark-blue', showButton, heading, text, buttonLabel, buttonUrl, showSecondaryButton, secondaryButtonLabel, secondaryButtonUrl } = attributes;
    const sectionClass = `sb-cta alignfull${background !== 'dark-blue' ? ` sb-block-bg--${background}` : ''}`;

    return (
        <section className={sectionClass}>
            <div className="sb-container">
                <RichText.Content tagName="h2" value={heading} />
                <RichText.Content tagName="p" value={text} />
                {showButton && !showSecondaryButton && (
                    <a className="sb-btn sb-btn--light" href={buttonUrl}>
                        {buttonLabel}
                    </a>
                )}
                {showButton && showSecondaryButton && (
                    <div className="sb-cta__buttons">
                        <a className="sb-btn sb-btn--light" href={buttonUrl}>{buttonLabel}</a>
                        <a className="sb-btn sb-cta__secondary" href={secondaryButtonUrl}>{secondaryButtonLabel}</a>
                    </div>
                )}
            </div>
        </section>
    );
}
