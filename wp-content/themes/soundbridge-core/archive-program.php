<?php
get_header();

$archive_settings = function_exists('soundbridge_get_program_archive_settings')
    ? soundbridge_get_program_archive_settings()
    : array();

$filter_taxonomies = array('age' => 'program_age', 'level' => 'program_level', 'instrument' => 'program_instrument');
$filters = array();
foreach ($filter_taxonomies as $filter_key => $taxonomy) {
    $filters[$filter_key] = isset($_GET[$filter_key]) ? sanitize_title(wp_unslash($_GET[$filter_key])) : '';
}
$filters['type'] = isset($_GET['type']) ? sanitize_title(wp_unslash($_GET['type'])) : '';

$tax_query = array('relation' => 'AND');
foreach ($filter_taxonomies as $filter_key => $taxonomy) {
    if ($filters[$filter_key]) {
        $tax_query[] = array('taxonomy' => $taxonomy, 'field' => 'slug', 'terms' => $filters[$filter_key]);
    }
}
if ($filters['type']) {
    $tax_query[] = array('taxonomy' => 'program_type', 'field' => 'slug', 'terms' => $filters['type']);
}
$query_args = array('post_type' => 'program', 'post_parent' => 0, 'posts_per_page' => 12, 'paged' => max(1, get_query_var('paged')));
if (count($tax_query) > 1) $query_args['tax_query'] = $tax_query;
$programs = new WP_Query($query_args);

$program_ids = get_posts(array('post_type' => 'program', 'post_parent' => 0, 'posts_per_page' => -1, 'post_status' => 'publish', 'fields' => 'ids'));
$filter_options = array();
foreach ($filter_taxonomies as $filter_key => $taxonomy) {
    $terms = get_terms(array('taxonomy' => $taxonomy, 'hide_empty' => true));
    $filter_options[$filter_key] = is_wp_error($terms) ? array() : $terms;
}

$program_types = get_terms(array('taxonomy' => 'program_type', 'hide_empty' => true));
$hero_image = !empty($archive_settings['hero_image_id'])
    ? wp_get_attachment_image_url($archive_settings['hero_image_id'], 'full')
    : '';
if (!$hero_image) {
    foreach ($program_ids as $program_id) {
        if (has_post_thumbnail($program_id)) {
            $hero_image = get_the_post_thumbnail_url($program_id, 'full');
            break;
        }
    }
}
$status_labels = array('open' => 'Enrolling now', 'coming-soon' => 'Coming soon', 'closed' => 'Registration closed');
?>

<nav class="sb-archive-breadcrumbs" aria-label="Breadcrumb">
    <div class="sb-container"><a href="<?php echo esc_url(home_url('/')); ?>">Home</a><span aria-hidden="true">›</span><span aria-current="page">Programs</span></div>
</nav>

<section class="sb-program-archive-hero<?php echo $hero_image ? ' has-image' : ''; ?>"<?php echo $hero_image ? ' style="--sb-program-hero-image: url(' . esc_url($hero_image) . ');"' : ''; ?>>
    <div class="sb-container sb-program-archive-hero__content">
        <?php if (!empty($archive_settings['hero_eyebrow'])) : ?><p class="sb-eyebrow"><?php echo wp_kses_post($archive_settings['hero_eyebrow']); ?></p><?php endif; ?>
        <h1><?php echo wp_kses_post($archive_settings['hero_heading'] ?? 'Programs'); ?><?php if (!empty($archive_settings['hero_highlight'])) : ?><br><em class="sb-highlight"><?php echo wp_kses_post($archive_settings['hero_highlight']); ?></em><?php endif; ?></h1>
        <?php if (!empty($archive_settings['hero_description'])) : ?><p class="sb-lead"><?php echo wp_kses_post($archive_settings['hero_description']); ?></p><?php endif; ?>
    </div>
</section>

<section class="sb-program-filters" aria-label="Program filters">
    <form class="sb-container sb-program-filters__form" method="get">
        <strong>Filter by:</strong>
        <?php foreach (array('age' => 'Age', 'level' => 'Level', 'instrument' => 'Instrument') as $filter_key => $filter_label) : ?>
            <label>
                <span><?php echo esc_html($filter_label); ?></span>
                <select name="<?php echo esc_attr($filter_key); ?>">
                    <option value="">All <?php echo esc_html('age' === $filter_key ? 'Ages' : $filter_label . 's'); ?></option>
                    <?php foreach ($filter_options[$filter_key] as $option) : ?>
                        <option value="<?php echo esc_attr($option->slug); ?>" <?php selected($filters[$filter_key], $option->slug); ?>><?php echo esc_html($option->name); ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
        <?php endforeach; ?>
        <label>
            <span>Type</span>
            <select name="type">
                <option value="">All Types</option>
                <?php if (!is_wp_error($program_types)) : foreach ($program_types as $program_type) : ?>
                    <option value="<?php echo esc_attr($program_type->slug); ?>" <?php selected($filters['type'], $program_type->slug); ?>><?php echo esc_html($program_type->name); ?></option>
                <?php endforeach; endif; ?>
            </select>
        </label>
        <button class="sb-btn sb-program-filters__submit" type="submit">Apply Filters</button>
        <?php if (array_filter($filters)) : ?><a class="sb-program-filters__clear" href="<?php echo esc_url(get_post_type_archive_link('program')); ?>">Clear filters ×</a><?php endif; ?>
    </form>
</section>

<section class="sb-program-archive sb-block-bg sb-block-bg--white">
    <div class="sb-container">
        <p class="sb-program-archive__count"><?php echo esc_html(sprintf(_n('%d program found', '%d programs found', $programs->found_posts, 'soundbridge-core'), $programs->found_posts)); ?></p>
        <?php if ($programs->have_posts()) : ?>
            <div class="sb-program-grid">
                <?php while ($programs->have_posts()) : $programs->the_post();
                    $program_id = get_the_ID();
                    $status = get_post_meta($program_id, 'sb_status', true) ?: 'open';
                    $has_scholarship = (bool) get_post_meta($program_id, 'sb_scholarship', true);
                    $registration_url = get_post_meta($program_id, 'sb_registration_url', true) ?: get_permalink();
                    $terms = get_the_terms($program_id, 'program_type');
                    $program_type = $terms && !is_wp_error($terms) ? $terms[0]->name : 'Program';
                    $description = get_post_meta($program_id, 'sb_about', true) ?: get_post_meta($program_id, 'sb_tagline', true) ?: get_the_excerpt();
                    $program_term_value = static function ($taxonomy) use ($program_id) {
                        $names = function_exists('soundbridge_get_program_term_names')
                            ? soundbridge_get_program_term_names($program_id, $taxonomy)
                            : wp_get_post_terms($program_id, $taxonomy, array('fields' => 'names'));
                        return !is_wp_error($names) && $names ? implode(', ', $names) : '';
                    };
                    $details = array(
                        'Age' => function_exists('soundbridge_get_program_age_label') ? soundbridge_get_program_age_label($program_id) : $program_term_value('program_age'),
                        'Level' => function_exists('soundbridge_get_program_level_label') ? soundbridge_get_program_level_label($program_id) : $program_term_value('program_level'),
                        'Instrument' => $program_term_value('program_instrument'),
                        'Schedule' => function_exists('soundbridge_get_program_meta') ? soundbridge_get_program_meta($program_id, 'schedule') : get_post_meta($program_id, 'sb_schedule', true),
                        'Location' => function_exists('soundbridge_get_program_meta') ? soundbridge_get_program_meta($program_id, 'location') : get_post_meta($program_id, 'sb_location', true),
                    );
                ?>
                    <article class="sb-program-card">
                        <div class="sb-program-card__media">
                            <?php if (has_post_thumbnail()) the_post_thumbnail('large', array('loading' => 'lazy')); ?>
                            <div class="sb-program-card__badges">
                                <span class="sb-program-card__status sb-program-card__status--<?php echo esc_attr($status); ?>"><?php echo esc_html($status_labels[$status] ?? $status_labels['coming-soon']); ?></span>
                                <?php if ($has_scholarship) : ?><span class="sb-program-card__scholarship">Scholarships Available</span><?php endif; ?>
                            </div>
                        </div>
                        <div class="sb-program-card__content">
                            <p class="sb-program-card__type"><?php echo esc_html($program_type); ?></p>
                            <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                            <?php if ($description) : ?><p class="sb-program-card__description"><?php echo esc_html(wp_trim_words($description, 25, '…')); ?></p><?php endif; ?>
                            <?php if (array_filter($details)) : ?><dl class="sb-program-card__details">
                                <?php foreach ($details as $label => $value) : if (!$value) continue; ?>
                                    <div class="<?php echo 'Location' === $label ? 'is-wide' : ''; ?>"><dt><?php echo esc_html($label); ?></dt><dd><?php echo esc_html($value); ?></dd></div>
                                <?php endforeach; ?>
                            </dl><?php endif; ?>
                            <div class="sb-program-card__actions">
                                <a class="sb-btn sb-btn--outline" href="<?php the_permalink(); ?>">View Program</a>
                                <?php if ('open' === $status) : ?><a class="sb-btn" href="<?php echo esc_url($registration_url); ?>">Register Now</a><?php endif; ?>
                            </div>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
            <?php echo wp_kses_post(paginate_links(array('total' => $programs->max_num_pages, 'current' => max(1, get_query_var('paged')), 'type' => 'list', 'add_args' => array_filter($filters)))); ?>
        <?php else : ?>
            <div class="sb-program-grid__empty"><div aria-hidden="true">♪</div><h2>No programs match your filters</h2><p>Try adjusting your filters or contact us — more programs are coming soon.</p><a class="sb-btn" href="<?php echo esc_url(get_post_type_archive_link('program')); ?>">Clear Filters</a></div>
        <?php endif; wp_reset_postdata(); ?>

        <aside class="sb-program-archive__callout">
            <div>
                <?php if (!empty($archive_settings['callout_eyebrow'])) : ?><p class="sb-eyebrow"><?php echo wp_kses_post($archive_settings['callout_eyebrow']); ?></p><?php endif; ?>
                <h2><?php echo wp_kses_post($archive_settings['callout_heading'] ?? ''); ?></h2>
                <?php if (!empty($archive_settings['callout_description'])) : ?><p><?php echo wp_kses_post($archive_settings['callout_description']); ?></p><?php endif; ?>
            </div>
            <div class="sb-program-archive__callout-actions">
                <?php if (!empty($archive_settings['callout_primary_label'])) : ?><a class="sb-btn sb-btn--outline" href="<?php echo esc_url($archive_settings['callout_primary_url']); ?>"><?php echo esc_html($archive_settings['callout_primary_label']); ?></a><?php endif; ?>
                <?php if (!empty($archive_settings['callout_second_label'])) : ?><a class="sb-btn" href="<?php echo esc_url($archive_settings['callout_second_url']); ?>"><?php echo esc_html($archive_settings['callout_second_label']); ?></a><?php endif; ?>
            </div>
        </aside>
    </div>
</section>

<section class="sb-program-archive-cta">
    <div class="sb-container">
        <div>
            <?php if (!empty($archive_settings['cta_eyebrow'])) : ?><p class="sb-eyebrow"><?php echo wp_kses_post($archive_settings['cta_eyebrow']); ?></p><?php endif; ?>
            <h2><?php echo wp_kses_post($archive_settings['cta_heading'] ?? ''); ?></h2>
            <?php if (!empty($archive_settings['cta_description'])) : ?><p><?php echo wp_kses_post($archive_settings['cta_description']); ?></p><?php endif; ?>
        </div>
        <?php if (!empty($archive_settings['cta_button_label'])) : ?><a class="sb-btn" href="<?php echo esc_url($archive_settings['cta_button_url']); ?>"><?php echo esc_html($archive_settings['cta_button_label']); ?></a><?php endif; ?>
    </div>
</section>

<?php get_footer(); ?>
