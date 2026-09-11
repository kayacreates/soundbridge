<?php
get_header();

$archive_settings = function_exists('soundbridge_get_event_archive_settings')
    ? soundbridge_get_event_archive_settings()
    : array();
$events = new WP_Query(array(
    'post_type'      => 'event',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'meta_key'       => 'sb_event_date',
    'orderby'        => 'meta_value',
    'order'          => 'ASC',
));
$today = current_time('Y-m-d');
$hero_image = $archive_settings['hero_image_url'] ?? 'https://images.unsplash.com/photo-1563902321212-bd2d1170c543?w=1600&h=700&fit=crop&auto=format';
?>

<nav class="sb-event-archive-breadcrumbs" aria-label="Breadcrumb">
    <div class="sb-container"><a href="<?php echo esc_url(home_url('/')); ?>">Home</a><span aria-hidden="true">›</span><span aria-current="page">Events</span></div>
</nav>

<section class="sb-event-archive-hero" style="--sb-event-hero-image: url('<?php echo esc_url($hero_image); ?>');">
    <div class="sb-container sb-event-archive-hero__content">
        <?php if (!empty($archive_settings['hero_eyebrow'])) : ?><p class="sb-eyebrow"><?php echo wp_kses_post($archive_settings['hero_eyebrow']); ?></p><?php endif; ?>
        <h1><?php echo wp_kses_post($archive_settings['hero_heading'] ?? 'Music Happening in'); ?><?php if (!empty($archive_settings['hero_highlight'])) : ?><br><em class="sb-highlight"><?php echo wp_kses_post($archive_settings['hero_highlight']); ?></em><?php endif; ?></h1>
        <?php if (!empty($archive_settings['hero_description'])) : ?><p class="sb-lead"><?php echo wp_kses_post($archive_settings['hero_description']); ?></p><?php endif; ?>
    </div>
</section>

<main class="sb-event-archive" data-sb-event-archive>
    <nav class="sb-event-archive__tabs" aria-label="Event date filters">
        <div class="sb-container" role="tablist">
            <button class="is-active" type="button" role="tab" aria-selected="true" data-sb-event-tab="upcoming">Upcoming Events</button>
            <button type="button" role="tab" aria-selected="false" data-sb-event-tab="past">Past Events</button>
        </div>
    </nav>

    <section class="sb-event-archive__results">
        <div class="sb-container">
            <div class="sb-event-archive__grid" data-sb-event-grid>
                <?php while ($events->have_posts()) : $events->the_post();
                    $post_id = get_the_ID();
                    $event_date = get_post_meta($post_id, 'sb_event_date', true);
                    $timestamp = $event_date ? strtotime($event_date) : false;
                    $normalized_date = $timestamp ? wp_date('Y-m-d', $timestamp) : '';
                    $period = !$normalized_date || $normalized_date >= $today ? 'upcoming' : 'past';
                    $terms = get_the_terms($post_id, 'event_type');
                    $term = $terms && !is_wp_error($terms) ? $terms[0] : null;
                    $event_type = $term ? $term->name : 'Program';
                    $type_slug = $term ? $term->slug : 'program';
                    $description = get_the_excerpt() ?: wp_strip_all_tags(get_the_content());
                    $details = array(
                        'time'     => get_post_meta($post_id, 'sb_event_time', true),
                        'location' => get_post_meta($post_id, 'sb_location', true),
                        'cost'     => get_post_meta($post_id, 'sb_cost', true),
                    );
                ?>
                    <article class="sb-event-card sb-event-card--<?php echo esc_attr($type_slug); ?>" data-sb-event-item data-period="<?php echo esc_attr($period); ?>"<?php echo 'past' === $period ? ' hidden' : ''; ?>>
                        <div class="sb-event-card__media">
                            <?php if (has_post_thumbnail()) : the_post_thumbnail('large', array('loading' => 'lazy')); endif; ?>
                            <time class="sb-event-card__date"<?php echo $normalized_date ? ' datetime="' . esc_attr($normalized_date) . '"' : ''; ?>>
                                <span><?php echo esc_html($timestamp ? wp_date('M', $timestamp) : 'TBD'); ?></span>
                                <strong><?php echo esc_html($timestamp ? wp_date('j', $timestamp) : '—'); ?></strong>
                            </time>
                            <span class="sb-event-card__type"><?php echo esc_html($event_type); ?></span>
                        </div>
                        <div class="sb-event-card__content">
                            <h2><?php the_title(); ?></h2>
                            <?php if ($description) : ?><p class="sb-event-card__description"><?php echo esc_html(wp_trim_words($description, 28, '…')); ?></p><?php endif; ?>
                            <dl class="sb-event-card__details">
                                <?php if ($details['time']) : ?><div><dt><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg><span class="screen-reader-text">Time</span></dt><dd><?php echo esc_html($details['time']); ?></dd></div><?php endif; ?>
                                <?php if ($details['location']) : ?><div><dt><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 5-5.5 10.5-7.4 12.3a.83.83 0 0 1-1.2 0C9.5 20.5 4 15 4 10a8 8 0 1 1 16 0"/><circle cx="12" cy="10" r="3"/></svg><span class="screen-reader-text">Location</span></dt><dd><?php echo esc_html($details['location']); ?></dd></div><?php endif; ?>
                                <?php if ($details['cost']) : ?><div><dt><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 9a3 3 0 0 0 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 0 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"/><path d="M13 5v2M13 17v2M13 11v2"/></svg><span class="screen-reader-text">Cost</span></dt><dd><?php echo esc_html($details['cost']); ?></dd></div><?php endif; ?>
                            </dl>
                            <a class="sb-btn" href="<?php the_permalink(); ?>">View Details →</a>
                        </div>
                    </article>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
            <div class="sb-event-archive__empty" data-sb-event-empty hidden>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                <h2 data-sb-event-empty-heading>No upcoming events right now</h2>
                <p>Check back soon — we’re always planning something new.</p>
            </div>
            <aside class="sb-event-archive__callout">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                <h2><?php echo wp_kses_post($archive_settings['callout_heading'] ?? 'Stay in the loop'); ?></h2>
                <?php if (!empty($archive_settings['callout_description'])) : ?><p><?php echo wp_kses_post($archive_settings['callout_description']); ?></p><?php endif; ?>
                <?php if (!empty($archive_settings['callout_button_label'])) : ?><a class="sb-btn" href="<?php echo esc_url($archive_settings['callout_button_url'] ?? ''); ?>"><?php echo esc_html($archive_settings['callout_button_label']); ?></a><?php endif; ?>
            </aside>
        </div>
    </section>
</main>

<?php get_footer(); ?>
