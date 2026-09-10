import { RichText } from '@wordpress/block-editor';

const StarRating = ({ rating }) => (
    <div className="sb-testimonials__stars" aria-label={`${rating} out of 5 stars`}>
        {Array.from({ length: 5 }, (_, index) => (
            <svg className={index < rating ? 'is-filled' : ''} key={index} viewBox="0 0 14 14" aria-hidden="true">
                <path d="M7 1l1.5 3 3.5.5-2.5 2.5.5 3.5L7 9l-3 1.5.5-3.5L2 4.5l3.5-.5z" />
            </svg>
        ))}
    </div>
);

export default function save({ attributes }) {
    const { background = 'white', eyebrow, heading, items = [] } = attributes;
    const sectionClass = `sb-testimonials sb-block-bg alignfull${background !== 'white' ? ` sb-block-bg--${background}` : ''}`;

    return (
        <section className={sectionClass}>
            <div className="sb-container">
                <div className="sb-testimonials__header">
                    {eyebrow && <RichText.Content tagName="p" className="sb-eyebrow" value={eyebrow} />}
                    <RichText.Content tagName="h2" value={heading} />
                </div>
                <div className="sb-testimonials__grid">
                    {items.map((item, index) => (
                        <article className="sb-testimonial" key={index}>
                            <StarRating rating={item.stars ?? 5} />
                            <RichText.Content tagName="p" className="sb-testimonial__quote" value={item.body || ''} />
                            <div className="sb-testimonial__author">
                                <span className="sb-testimonial__avatar" aria-hidden="true">{item.name?.trim().charAt(0) || '?'}</span>
                                <div>
                                    <RichText.Content tagName="p" className="sb-testimonial__name" value={item.name || ''} />
                                    <RichText.Content tagName="p" className="sb-testimonial__role" value={item.role || ''} />
                                </div>
                            </div>
                        </article>
                    ))}
                </div>
            </div>
        </section>
    );
}
