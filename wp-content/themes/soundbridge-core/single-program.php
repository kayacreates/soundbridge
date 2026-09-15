<?php
get_header();

while (have_posts()) :
    the_post();

    $post_id = get_the_ID();
    $meta = static fn($key) => function_exists('soundbridge_get_program_meta')
        ? soundbridge_get_program_meta($post_id, $key)
        : get_post_meta($post_id, 'sb_' . $key, true);
    $lines = static fn($value) => array_values(array_filter(array_map('trim', preg_split('/\R/', (string) $value))));
    $rows = static function ($value, $columns) use ($lines) {
        return array_map(static function ($line) use ($columns) {
            return array_pad(array_slice(array_map('trim', explode('|', $line)), 0, $columns), $columns, '');
        }, $lines($value));
    };

    $status = $meta('status') ?: 'open';
    $status_labels = array('open' => 'Enrolling now', 'coming-soon' => 'Coming soon', 'closed' => 'Registration closed');
    $status_label = $status_labels[$status] ?? 'Coming soon';
    $registration_url = $meta('registration_url') ?: home_url('/contact');
    $registration_label = $meta('registration_label') ?: 'Register Now →';
    $question_url = $meta('question_url') ?: home_url('/contact');
    $scholarship_url = $meta('scholarship_url') ?: home_url('/scholarships');
    $scholarship_label = $meta('scholarship_label') ?: 'Learn about financial aid →';
    $has_scholarship = (bool) $meta('scholarship');
    $about = $meta('about');
    $learning_items = $lines($meta('learn'));
    $schedule_rows = $rows($meta('schedule_details'), 3);
    $schedule_items = function_exists('soundbridge_get_program_schedule_items') ? soundbridge_get_program_schedule_items($post_id) : array();
    $schedule_matrix = $schedule_items && function_exists('soundbridge_build_program_schedule_matrix') ? soundbridge_build_program_schedule_matrix($schedule_items) : array();
    $faculty_rows = $rows($meta('faculty'), 3);
    $faculty_ids = function_exists('soundbridge_get_program_faculty_ids') ? soundbridge_get_program_faculty_ids($post_id) : array();
    $faculty_members = $faculty_ids ? get_posts(array(
        'post_type' => 'faculty',
        'post_status' => 'publish',
        'post__in' => $faculty_ids,
        'posts_per_page' => -1,
        'orderby' => 'post__in',
    )) : array();
    $gallery_ids = array_filter(array_map('absint', explode(',', (string) $meta('gallery_ids'))));
    $gallery_urls = array_values(array_filter(array_map(static fn($attachment_id) => wp_get_attachment_image_url($attachment_id, 'large'), $gallery_ids)));
    if (!$gallery_urls) $gallery_urls = $lines($meta('gallery_urls'));
    $youtube_url = $meta('youtube_url');
    $youtube_embed = $youtube_url ? wp_oembed_get($youtube_url) : '';
    $testimonial_rows = $rows($meta('testimonials'), 3);
    $faq_rows = $rows($meta('faq'), 2);
    $bring_items = $lines($meta('what_to_bring'));
    $program_types = get_the_terms($post_id, 'program_type');
    $program_type = $program_types && !is_wp_error($program_types) ? $program_types[0]->name : 'Program';
    $program_term_value = static function ($taxonomy) use ($post_id) {
        $names = function_exists('soundbridge_get_program_term_names')
            ? soundbridge_get_program_term_names($post_id, $taxonomy)
            : wp_get_post_terms($post_id, $taxonomy, array('fields' => 'names'));
        return !is_wp_error($names) && $names ? implode(', ', $names) : '';
    };
    $program_age_label = function_exists('soundbridge_get_program_age_label') ? soundbridge_get_program_age_label($post_id) : $program_term_value('program_age');
    $program_level = function_exists('soundbridge_get_program_level_label') ? soundbridge_get_program_level_label($post_id) : $program_term_value('program_level');
    $program_instrument = function_exists('soundbridge_get_program_instrument_label') ? soundbridge_get_program_instrument_label($post_id) : $program_term_value('program_instrument');
    $info_items = array(
        array('label' => 'Age Range', 'value' => $program_age_label, 'icon' => '<path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>'),
        array('label' => 'Experience', 'value' => $program_level, 'icon' => '<circle cx="12" cy="8" r="6"/><path d="M15.5 13.5 17 22l-5-3-5 3 1.5-8.5"/>'),
        array('label' => 'Instrument', 'value' => $program_instrument, 'icon' => '<path d="M9 18V5l12-2v13"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="16" r="3"/>'),
        array('label' => 'Schedule', 'value' => $meta('schedule'), 'icon' => '<rect width="18" height="18" x="3" y="4" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>'),
        array('label' => 'Location', 'value' => $meta('location'), 'icon' => '<path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="3"/>'),
        array('label' => 'Cost', 'value' => $meta('cost'), 'icon' => '<rect width="20" height="14" x="2" y="5" rx="2"/><path d="M2 10h20"/>'),
    );
    $has_info_items = (bool) array_filter($info_items, static fn($item) => $item['value']);
    $audience_items = array(
        'Age Range' => $program_age_label,
        'Experience Level' => $program_level,
        'Instruments' => $program_instrument,
        'Location' => $meta('location'),
    );
    $parent_program = $post->post_parent ? get_post($post->post_parent) : null;
    $child_programs = get_posts(array(
        'post_type' => 'program',
        'post_status' => 'publish',
        'post_parent' => $post_id,
        'posts_per_page' => -1,
        'orderby' => array('menu_order' => 'ASC', 'title' => 'ASC'),
    ));
    ?>
    <nav class="sb-program-breadcrumb" aria-label="Breadcrumb">
        <div class="sb-container"><a href="<?php echo esc_url(home_url('/')); ?>">Home</a><span>›</span><a href="<?php echo esc_url(get_post_type_archive_link('program')); ?>">Programs</a><?php if ($parent_program) : ?><span>›</span><a href="<?php echo esc_url(get_permalink($parent_program)); ?>"><?php echo esc_html(get_the_title($parent_program)); ?></a><?php endif; ?><span>›</span><span aria-current="page"><?php the_title(); ?></span></div>
    </nav>

    <section class="sb-program-hero">
        <div class="sb-program-hero__image" <?php if (has_post_thumbnail()) : ?> style="background-image: url('<?php echo esc_url(get_the_post_thumbnail_url($post_id, 'full')); ?>')"<?php endif; ?>></div>
        <div class="sb-program-hero__overlay"></div>
        <div class="sb-container sb-program-hero__content">
            <div class="sb-program-badges">
                <span class="sb-program-status sb-program-status--<?php echo esc_attr($status); ?>"><?php echo esc_html($status_label); ?></span>
                <?php if ($has_scholarship) : ?><span class="sb-program-badge">Scholarships Available</span><?php endif; ?>
                <span class="sb-program-badge sb-program-badge--muted"><?php echo esc_html($program_type); ?></span>
            </div>
            <h1><?php the_title(); ?></h1>
            <?php if ($meta('tagline')) : ?><p><?php echo esc_html($meta('tagline')); ?></p><?php endif; ?>
            <?php if ('open' === $status) : ?><div class="sb-actions"><a class="sb-btn" href="<?php echo esc_url($registration_url); ?>"><?php echo esc_html($registration_label); ?></a><?php if ($has_scholarship) : ?><a class="sb-btn sb-btn--outline" href="<?php echo esc_url($scholarship_url); ?>">Scholarship Info</a><?php endif; ?></div><?php endif; ?>
        </div>
    </section>

    <?php if ($has_info_items) : ?><section class="sb-program-info" aria-label="Program overview">
        <div class="sb-container">
            <?php foreach ($info_items as $item) : if (!$item['value']) continue; ?>
                <div><span><svg class="sb-program-info__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><?php echo $item['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></svg><?php echo esc_html($item['label']); ?></span><strong><?php echo esc_html($item['value']); ?></strong></div>
            <?php endforeach; ?>
        </div>
    </section><?php endif; ?>

    <div class="sb-container sb-program-layout">
        <main class="sb-program-main">
            <?php if ($about || get_the_content()) : ?><section class="sb-program-section"><h2>About the Program</h2><div class="sb-program-copy"><?php echo $about ? wpautop(esc_html($about)) : apply_filters('the_content', get_the_content()); ?></div></section><?php endif; ?>

            <?php if ($child_programs) : ?><section class="sb-program-section sb-program-options"><h2>Choose Your Program</h2><p>Select the option that best fits your musician.</p><div class="sb-program-options__grid">
                <?php foreach ($child_programs as $child_program) :
                    $child_id = $child_program->ID;
                    $child_status = get_post_meta($child_id, 'sb_status', true) ?: 'open';
                    $child_about = get_post_meta($child_id, 'sb_about', true) ?: get_post_meta($child_id, 'sb_tagline', true);
                    $child_age = function_exists('soundbridge_get_program_age_label') ? soundbridge_get_program_age_label($child_id) : '';
                    $child_level = function_exists('soundbridge_get_program_level_label') ? soundbridge_get_program_level_label($child_id) : '';
                    ?>
                    <article class="sb-program-option-card">
                        <?php if (has_post_thumbnail($child_id)) : ?><a class="sb-program-option-card__image" href="<?php echo esc_url(get_permalink($child_id)); ?>"><?php echo get_the_post_thumbnail($child_id, 'large', array('loading' => 'lazy')); ?></a><?php endif; ?>
                        <div class="sb-program-option-card__content">
                            <span class="sb-program-status sb-program-status--<?php echo esc_attr($child_status); ?>"><?php echo esc_html($status_labels[$child_status] ?? 'Coming soon'); ?></span>
                            <h3><a href="<?php echo esc_url(get_permalink($child_id)); ?>"><?php echo esc_html(get_the_title($child_id)); ?></a></h3>
                            <?php if ($child_age || $child_level) : ?><p class="sb-program-option-card__details"><?php echo esc_html(implode(' · ', array_filter(array($child_age, $child_level)))); ?></p><?php endif; ?>
                            <?php if ($child_about) : ?><p><?php echo esc_html(wp_trim_words($child_about, 24, '…')); ?></p><?php endif; ?>
                            <a class="sb-btn sb-btn--outline" href="<?php echo esc_url(get_permalink($child_id)); ?>">View Program</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div></section><?php endif; ?>

            <?php if (array_filter($audience_items)) : ?><section class="sb-program-section sb-program-audience"><h2>Who It’s For</h2><div class="sb-program-audience__grid">
                <?php foreach ($audience_items as $label => $value) : if (!$value) continue; ?><div><span><?php echo esc_html($label); ?></span><strong><?php echo esc_html($value); ?></strong></div><?php endforeach; ?>
            </div></section><?php endif; ?>

            <?php if ($learning_items) : ?><section class="sb-program-section"><h2>What Students Will Learn</h2><ol class="sb-program-numbered-list"><?php foreach ($learning_items as $item) : ?><li><?php echo esc_html($item); ?></li><?php endforeach; ?></ol></section><?php endif; ?>

            <?php if (!empty($schedule_matrix['rows']) && !empty($schedule_matrix['days'])) : ?>
                <section class="sb-program-section">
                    <h2>Schedule</h2>
                    <div class="sb-program-schedule-table-wrap">
                        <table class="sb-program-schedule-table">
                            <thead>
                                <tr>
                                    <th scope="col"><span class="screen-reader-text"><?php esc_html_e('Time', 'soundbridge-core'); ?></span></th>
                                    <?php foreach ($schedule_matrix['days'] as $day_label) : ?>
                                        <th scope="col"><?php echo esc_html($day_label); ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($schedule_matrix['rows'] as $schedule_row) : ?>
                                    <tr>
                                        <th scope="row"><?php echo esc_html(soundbridge_format_schedule_time($schedule_row['start']) . ' – ' . soundbridge_format_schedule_time($schedule_row['end'])); ?></th>
                                        <?php foreach ($schedule_matrix['days'] as $day => $day_label) : ?>
                                            <?php $cell = $schedule_row['cells'][$day] ?? null; ?>
                                            <?php if (!empty($cell['skip'])) continue; ?>
                                            <?php if (!empty($cell['item'])) : $schedule_item = $cell['item']; ?>
                                                <td class="sb-program-schedule-table__activity sb-program-schedule-table__activity--<?php echo esc_attr(sanitize_html_class($schedule_item['type'] ?? 'other')); ?>" rowspan="<?php echo esc_attr($cell['rowspan']); ?>">
                                                    <strong><?php echo esc_html($schedule_item['title'] ?? ''); ?></strong>
                                                    <?php if (!empty($schedule_item['detail'])) : ?><span><?php echo esc_html($schedule_item['detail']); ?></span><?php endif; ?>
                                                </td>
                                            <?php else : ?>
                                                <td class="sb-program-schedule-table__empty"></td>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </section>
            <?php elseif ($schedule_rows) : ?>
                <section class="sb-program-section"><h2>Schedule</h2><div class="sb-program-schedule"><?php foreach ($schedule_rows as [$day, $time, $note]) : ?><div><strong><?php echo esc_html($day); ?></strong><span><?php echo esc_html($time); ?></span><p><?php echo esc_html($note); ?></p></div><?php endforeach; ?></div></section>
            <?php endif; ?>

            <?php if ($faculty_members) : ?>
                <section class="sb-program-section">
                    <h2>Faculty &amp; Instructors</h2>
                    <div class="sb-program-faculty">
                        <?php foreach ($faculty_members as $faculty_member) :
                            $faculty_role = get_post_meta($faculty_member->ID, 'sb_role', true);
                            $faculty_intro = $faculty_member->post_excerpt ?: wp_trim_words(wp_strip_all_tags($faculty_member->post_content), 28, '…');
                            ?>
                            <article>
                                <a class="sb-program-faculty__portrait" href="<?php echo esc_url(get_permalink($faculty_member)); ?>" aria-label="<?php echo esc_attr(sprintf('Read %s’s biography', $faculty_member->post_title)); ?>">
                                    <?php if (has_post_thumbnail($faculty_member)) : ?>
                                        <?php echo get_the_post_thumbnail($faculty_member, 'medium', array('loading' => 'lazy')); ?>
                                    <?php else : ?>
                                        <span class="sb-program-avatar" aria-hidden="true"><?php echo esc_html(substr($faculty_member->post_title, 0, 1)); ?></span>
                                    <?php endif; ?>
                                </a>
                                <div>
                                    <h3><a href="<?php echo esc_url(get_permalink($faculty_member)); ?>"><?php echo esc_html($faculty_member->post_title); ?></a></h3>
                                    <?php if ($faculty_role) : ?><strong><?php echo esc_html($faculty_role); ?></strong><?php endif; ?>
                                    <?php if ($faculty_intro) : ?><p><?php echo esc_html($faculty_intro); ?></p><?php endif; ?>
                                    <a class="sb-text-link" href="<?php echo esc_url(get_permalink($faculty_member)); ?>">Read full bio →</a>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php elseif ($faculty_rows) : ?>
                <section class="sb-program-section"><h2>Faculty &amp; Instructors</h2><div class="sb-program-faculty"><?php foreach ($faculty_rows as [$name, $role, $bio]) : ?><article><span class="sb-program-avatar" aria-hidden="true"><?php echo esc_html(substr($name, 0, 1)); ?></span><div><h3><?php echo esc_html($name); ?></h3><strong><?php echo esc_html($role); ?></strong><p><?php echo esc_html($bio); ?></p></div></article><?php endforeach; ?></div></section>
            <?php endif; ?>

            <?php if ($meta('cost') || $has_scholarship) : ?><section class="sb-program-section"><h2>Pricing &amp; Scholarships</h2><div class="sb-program-pricing">
                <?php if ($meta('cost')) : ?><div><span>Tuition</span><h3><?php echo esc_html($meta('cost')); ?></h3><p><?php echo esc_html($meta('cost_detail')); ?></p></div><?php endif; ?>
                <?php if ($has_scholarship) : ?><div class="sb-program-pricing__aid"><span>Financial Aid</span><h3>Scholarships Available</h3><p><?php echo esc_html($meta('scholarship_detail')); ?></p><a href="<?php echo esc_url($scholarship_url); ?>"><?php echo esc_html($scholarship_label); ?></a></div><?php endif; ?>
            </div></section><?php endif; ?>

            <?php if ($bring_items) : ?><section class="sb-program-section"><h2>What to Bring</h2><ul class="sb-program-check-list"><?php foreach ($bring_items as $item) : ?><li><?php echo esc_html($item); ?></li><?php endforeach; ?></ul></section><?php endif; ?>

            <?php if ($gallery_urls) : ?><section class="sb-program-section"><h2>Photo Gallery</h2><div class="sb-program-gallery"><?php foreach ($gallery_urls as $index => $url) : ?><img class="<?php echo $index === 0 ? 'is-featured' : ''; ?>" src="<?php echo esc_url($url); ?>" alt="<?php echo esc_attr(get_the_title() . ' photo ' . ($index + 1)); ?>" loading="lazy"><?php endforeach; ?></div></section><?php endif; ?>

            <?php if ($youtube_url) : ?><section class="sb-program-section sb-program-video-section"><h2>Program Video</h2><?php if ($youtube_embed) : ?><div class="sb-program-video"><?php echo $youtube_embed; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div><?php else : ?><a class="sb-text-link" href="<?php echo esc_url($youtube_url); ?>">Watch on YouTube →</a><?php endif; ?></section><?php endif; ?>

            <?php if ($testimonial_rows) : ?><section class="sb-program-section sb-program-testimonial-carousel" data-program-testimonials><header class="sb-program-testimonial-carousel__header"><h2>What Parents Say</h2><?php if (count($testimonial_rows) > 1) : ?><div class="sb-program-testimonial-carousel__controls"><button type="button" data-carousel-previous aria-label="Show previous testimonial"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg></button><button type="button" data-carousel-next aria-label="Show next testimonial"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m9 18 6-6-6-6"/></svg></button></div><?php endif; ?></header><div class="sb-program-testimonial-carousel__viewport"><div class="sb-program-testimonials"><?php foreach ($testimonial_rows as $index => [$quote, $name, $role]) : ?><blockquote aria-hidden="<?php echo $index > 1 ? 'true' : 'false'; ?>"><p>“<?php echo esc_html($quote); ?>”</p><footer><strong><?php echo esc_html($name); ?></strong><span><?php echo esc_html($role); ?></span></footer></blockquote><?php endforeach; ?></div></div></section><?php endif; ?>

            <?php if ($faq_rows) : ?><section class="sb-program-section"><h2>Frequently Asked Questions</h2><div class="sb-program-faq"><?php foreach ($faq_rows as [$question, $answer]) : ?><details><summary><?php echo esc_html($question); ?></summary><p><?php echo esc_html($answer); ?></p></details><?php endforeach; ?></div><a class="sb-text-link" href="<?php echo esc_url(home_url('/faq')); ?>">View all FAQs →</a></section><?php endif; ?>
        </main>

        <aside class="sb-program-sidebar" id="register"><div class="sb-program-sidebar__card">
            <span class="sb-program-status sb-program-status--<?php echo esc_attr($status); ?>"><?php echo esc_html($status_label); ?></span><h3><?php the_title(); ?></h3><p><?php echo esc_html(implode(' · ', array_filter(array($meta('schedule'), $meta('location'))))); ?></p>
            <?php foreach (array('Age Range' => $program_age_label, 'Level' => $program_level, 'Cost' => $meta('cost')) as $label => $value) : if (!$value) continue; ?><div class="sb-program-sidebar__row"><span><?php echo esc_html($label); ?></span><strong><?php echo esc_html($value); ?></strong></div><?php endforeach; ?>
            <div class="sb-program-sidebar__actions"><?php if ('open' === $status) : ?><a class="sb-btn" href="<?php echo esc_url($registration_url); ?>"><?php echo esc_html($registration_label); ?></a><?php endif; ?><a class="sb-btn sb-btn--outline" href="<?php echo esc_url($question_url); ?>">Ask a Question</a></div>
            <?php if ($has_scholarship) : ?><div class="sb-program-sidebar__aid"><strong>🎓 Scholarships Available</strong><a href="<?php echo esc_url($scholarship_url); ?>"><?php echo esc_html($scholarship_label); ?></a></div><?php endif; ?>
        </div></aside>
    </div>

    <div class="sb-program-mobile-cta<?php echo 'open' === $status ? ' has-registration' : ''; ?>"><a class="sb-btn sb-btn--outline" href="<?php echo esc_url($question_url); ?>">Ask a Question</a><?php if ('open' === $status) : ?><a class="sb-btn" href="<?php echo esc_url($registration_url); ?>"><?php echo esc_html($registration_label); ?></a><?php endif; ?></div>
    <?php
endwhile;

get_footer();
