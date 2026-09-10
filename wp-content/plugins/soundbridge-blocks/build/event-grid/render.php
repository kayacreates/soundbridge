<?php
/**
 * Server-rendered Events block.
 *
 * @var array $attributes Block attributes.
 */

$event_query = new WP_Query(
    [
        'post_type'      => 'event',
        'posts_per_page' => max(1, (int) ($attributes['count'] ?? 3)),
        'meta_key'       => 'sb_event_date',
        'orderby'        => 'meta_value',
        'order'          => 'ASC',
        'meta_query'     => [
            [
                'key'     => 'sb_event_date',
                'value'   => current_time('Y-m-d'),
                'compare' => '>=',
                'type'    => 'DATE',
            ],
        ],
    ]
);

$eyebrow     = $attributes['eyebrow'] ?? 'News & Events';
$heading     = $attributes['heading'] ?? 'News about SoundBridge';
$show_button = $attributes['showButton'] ?? true;
$button_text = $attributes['buttonLabel'] ?? 'See All Events →';
$button_url  = $attributes['buttonUrl'] ?? '/events';
$background  = $attributes['background'] ?? 'pale-blue';
$section_class = 'sb-events sb-section alignfull ' . ($background === 'pale-blue' ? 'sb-section--pale' : 'sb-block-bg--' . $background);
?>
<section <?php echo get_block_wrapper_attributes(['class' => $section_class]); ?>>
    <div class="sb-container">
        <div class="sb-events__header">
            <div>
                <?php if ($eyebrow) : ?>
                    <p class="sb-events__eyebrow"><?php echo esc_html($eyebrow); ?></p>
                <?php endif; ?>
                <h2><?php echo wp_kses_post($heading); ?></h2>
            </div>
            <?php if ($show_button && $button_text) : ?>
                <a class="sb-btn sb-events__button" href="<?php echo esc_url($button_url); ?>"><?php echo esc_html($button_text); ?></a>
            <?php endif; ?>
        </div>

        <?php if ($event_query->have_posts()) : ?>
            <div class="sb-events__list">
                <?php while ($event_query->have_posts()) : $event_query->the_post(); ?>
                    <?php
                    $event_date = get_post_meta(get_the_ID(), 'sb_event_date', true);
                    $timestamp  = $event_date ? strtotime($event_date) : false;
                    $terms      = get_the_terms(get_the_ID(), 'event_type');
                    $event_type = $terms && !is_wp_error($terms) ? $terms[0]->name : 'Event';
                    ?>
                    <article class="sb-event-row">
                        <time class="sb-event-row__date"<?php echo $event_date ? ' datetime="' . esc_attr($event_date) . '"' : ''; ?>>
                            <?php echo esc_html($timestamp ? wp_date('M j, Y', $timestamp) : $event_date); ?>
                        </time>
                        <span class="sb-event-row__type"><?php echo esc_html($event_type); ?></span>
                        <h3 class="sb-event-row__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                        <a class="sb-event-row__link" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr(sprintf('View %s', get_the_title())); ?>">
                            <svg viewBox="0 0 14 14" aria-hidden="true"><path d="M3 7h8M7 3l4 4-4 4" /></svg>
                        </a>
                    </article>
                <?php endwhile; ?>
            </div>
        <?php else : ?>
            <p class="sb-events__empty">No upcoming events are currently scheduled.</p>
        <?php endif; ?>
    </div>
</section>
<?php wp_reset_postdata(); ?>
