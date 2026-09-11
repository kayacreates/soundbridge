<?php
get_header();

while (have_posts()) : the_post();
    $post_id = get_the_ID();
    $meta = static fn($key) => get_post_meta($post_id, 'sb_' . $key, true);
    $event_date = $meta('event_date');
    $timestamp = $event_date ? strtotime($event_date) : false;
    $date_display = $timestamp ? wp_date('F j, Y', $timestamp) : $event_date;
    $event_time = $meta('event_time');
    $location = $meta('location');
    $address = $meta('address');
    $cost = $meta('cost');
    $audience = $meta('audience');
    $description = $meta('description');
    $registration_url = $meta('registration_url');
    $registration_label = $meta('registration_label') ?: (stripos($cost, 'free') !== false ? 'Add to Calendar' : 'Register / Get Tickets');
    $terms = get_the_terms($post_id, 'event_type');
    $event_type = $terms && !is_wp_error($terms) ? $terms[0]->name : 'Event';
    $hero_image = get_the_post_thumbnail_url($post_id, 'full');
    $period = $timestamp && wp_date('Y-m-d', $timestamp) < current_time('Y-m-d') ? 'past' : 'upcoming';
    $related = new WP_Query(array(
        'post_type'      => 'event',
        'post_status'    => 'publish',
        'posts_per_page' => 2,
        'post__not_in'   => array($post_id),
        'meta_key'       => 'sb_event_date',
        'orderby'        => 'meta_value',
        'order'          => 'ASC',
        'meta_query'     => array(array(
            'key'     => 'sb_event_date',
            'value'   => current_time('Y-m-d'),
            'compare' => 'past' === $period ? '<' : '>=',
            'type'    => 'DATE',
        )),
    ));
    $map_query = trim(implode(', ', array_filter(array($location, $address))));
    ?>
    <nav class="sb-event-breadcrumbs" aria-label="Breadcrumb">
        <div class="sb-container"><a href="<?php echo esc_url(home_url('/')); ?>">Home</a><span aria-hidden="true">›</span><a href="<?php echo esc_url(get_post_type_archive_link('event')); ?>">Events</a><span aria-hidden="true">›</span><span aria-current="page"><?php the_title(); ?></span></div>
    </nav>

    <section class="sb-single-event-hero<?php echo $hero_image ? ' has-image' : ''; ?>"<?php echo $hero_image ? ' style="--sb-event-image: url(' . esc_url($hero_image) . ');"' : ''; ?>>
        <div class="sb-container sb-single-event-hero__content">
            <span class="sb-single-event-hero__type"><?php echo esc_html($event_type); ?></span>
            <h1><?php the_title(); ?></h1>
            <div class="sb-single-event-hero__details">
                <?php if ($date_display) : ?><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg><?php echo esc_html($date_display); ?></span><?php endif; ?>
                <?php if ($event_time) : ?><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg><?php echo esc_html($event_time); ?></span><?php endif; ?>
                <?php if ($location) : ?><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 5-5.5 10.5-7.4 12.3a.83.83 0 0 1-1.2 0C9.5 20.5 4 15 4 10a8 8 0 1 1 16 0"/><circle cx="12" cy="10" r="3"/></svg><?php echo esc_html($location); ?></span><?php endif; ?>
                <?php if ($cost) : ?><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 9a3 3 0 0 0 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 0 0 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"/><path d="M13 5v2M13 17v2M13 11v2"/></svg><?php echo esc_html($cost); ?></span><?php endif; ?>
            </div>
        </div>
    </section>

    <div class="sb-container sb-single-event-layout">
        <main class="sb-single-event-main">
            <?php if ($description) : ?><section class="sb-single-event-section"><h2>About This Event</h2><div class="sb-single-event-copy"><?php echo wpautop(esc_html($description)); ?></div><?php if ($audience) : ?><p><?php echo esc_html('Everyone welcome' === $audience ? 'This event is free and open to the entire community. Bring your family and friends.' : 'This event is intended for: ' . $audience . '.'); ?></p><?php endif; ?></section><?php endif; ?>

            <section class="sb-single-event-section sb-single-event-details">
                <h2>Event Details</h2>
                <dl>
                    <?php foreach (array('Date' => $date_display, 'Time' => $event_time, 'Location' => $location, 'Admission' => $cost, 'Audience' => $audience) as $label => $value) : if (!$value) continue; ?><div><dt><?php echo esc_html($label); ?></dt><dd><?php echo esc_html($value); ?></dd></div><?php endforeach; ?>
                </dl>
            </section>

            <?php if ($location || $address) : ?><section class="sb-single-event-section sb-single-event-venue"><h2>Venue</h2><div><strong><?php echo esc_html($location); ?></strong><?php if ($address) : ?><p><?php echo esc_html($address); ?></p><?php endif; ?><div class="sb-single-event-map"><svg viewBox="0 0 32 32" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M16 3C10.5 3 6 7.5 6 13c0 7 10 16 10 16s10-9 10-16c0-5.5-4.5-10-10-10z"/><circle cx="16" cy="13" r="3"/></svg><span>Map view coming soon</span><a href="https://maps.google.com/?q=<?php echo rawurlencode($map_query); ?>" target="_blank" rel="noopener noreferrer">Open in Google Maps →</a></div></div></section><?php endif; ?>

            <?php if ($related->have_posts()) : ?><section class="sb-single-event-section sb-related-events"><h2>Related Events</h2><div><?php while ($related->have_posts()) : $related->the_post(); $related_date = get_post_meta(get_the_ID(), 'sb_event_date', true); $related_timestamp = $related_date ? strtotime($related_date) : false; ?><a href="<?php the_permalink(); ?>"><span class="sb-related-events__image"><?php if (has_post_thumbnail()) the_post_thumbnail('medium_large', array('loading' => 'lazy')); ?></span><span class="sb-related-events__content"><strong><?php echo esc_html($related_timestamp ? wp_date('F j, Y', $related_timestamp) : $related_date); ?></strong><span><?php the_title(); ?></span></span></a><?php endwhile; ?></div></section><?php endif; wp_reset_postdata(); ?>
        </main>

        <aside class="sb-single-event-sidebar">
            <div class="sb-single-event-summary">
                <h2><?php the_title(); ?></h2>
                <?php if ($date_display || $event_time) : ?><p><?php echo esc_html(implode(' · ', array_filter(array($date_display, $event_time)))); ?></p><?php endif; ?>
                <dl><?php foreach (array('Location' => $location, 'Admission' => $cost, 'Audience' => $audience) as $label => $value) : if (!$value) continue; ?><div><dt><?php echo esc_html($label); ?></dt><dd><?php echo esc_html($value); ?></dd></div><?php endforeach; ?></dl>
                <div class="sb-single-event-summary__actions">
                    <?php if ($registration_url) : ?><a class="sb-btn" href="<?php echo esc_url($registration_url); ?>"><?php echo esc_html($registration_label); ?></a><?php endif; ?>
                    <a class="sb-btn sb-btn--outline" href="<?php echo esc_url(home_url('/contact/')); ?>">Contact Us</a>
                </div>
            </div>
            <a class="sb-single-event-sidebar__back" href="<?php echo esc_url(get_post_type_archive_link('event')); ?>">← Back to all events</a>
        </aside>
    </div>
    <?php
endwhile;

get_footer();
