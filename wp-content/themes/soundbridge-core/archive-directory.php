<?php
get_header();

$archive_settings = function_exists('soundbridge_get_directory_archive_settings')
    ? soundbridge_get_directory_archive_settings()
    : array();
$directory = new WP_Query(array(
    'post_type'      => 'directory',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'orderby'        => 'title',
    'order'          => 'ASC',
));
$categories = get_terms(array('taxonomy' => 'directory_category', 'hide_empty' => true));
$hero_image = $archive_settings['hero_image_url'] ?? 'https://images.unsplash.com/photo-1510915361894-db8b60106cb1?w=1600&h=700&fit=crop&auto=format';
?>

<nav class="sb-archive-breadcrumbs" aria-label="Breadcrumb">
    <div class="sb-container"><a href="<?php echo esc_url(home_url('/')); ?>">Home</a><span aria-hidden="true">›</span><span aria-current="page">Music Directory</span></div>
</nav>

<section class="sb-directory-hero" style="--sb-directory-hero-image: url('<?php echo esc_url($hero_image); ?>');">
    <div class="sb-container sb-directory-hero__content">
        <?php if (!empty($archive_settings['hero_eyebrow'])) : ?><p class="sb-eyebrow"><?php echo wp_kses_post($archive_settings['hero_eyebrow']); ?></p><?php endif; ?>
        <h1><?php echo wp_kses_post($archive_settings['hero_heading'] ?? 'Saginaw Bay'); ?><?php if (!empty($archive_settings['hero_highlight'])) : ?><br><em class="sb-highlight"><?php echo wp_kses_post($archive_settings['hero_highlight']); ?></em><?php endif; ?></h1>
        <?php if (!empty($archive_settings['hero_description'])) : ?><p class="sb-lead"><?php echo wp_kses_post($archive_settings['hero_description']); ?></p><?php endif; ?>
    </div>
</section>

<main class="sb-directory" data-sb-directory>
    <section class="sb-directory__filters" aria-label="Directory filters">
        <div class="sb-container sb-directory__filter-inner">
            <label class="sb-directory__search">
                <span>Search</span>
                <span class="sb-directory__search-field">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    <input type="search" placeholder="Search by name, instrument, description…" aria-label="Search directory" data-sb-directory-search>
                </span>
            </label>
            <div class="sb-directory__categories">
                <span>Category</span>
                <div>
                    <button class="is-active" type="button" data-sb-directory-category="all">All</button>
                    <?php if (!is_wp_error($categories)) : foreach ($categories as $category) : ?>
                        <button type="button" data-sb-directory-category="<?php echo esc_attr($category->slug); ?>"><?php echo esc_html($category->name); ?></button>
                    <?php endforeach; endif; ?>
                </div>
            </div>
            <button class="sb-directory__clear" type="button" data-sb-directory-clear hidden>Clear ×</button>
        </div>
    </section>

    <section class="sb-directory__results sb-block-bg sb-block-bg--white">
        <div class="sb-container">
            <p class="sb-directory__count" aria-live="polite" data-sb-directory-count></p>
            <div class="sb-directory__grid" data-sb-directory-grid>
                <?php while ($directory->have_posts()) : $directory->the_post();
                    $post_id = get_the_ID();
                    $terms = get_the_terms($post_id, 'directory_category');
                    $term = $terms && !is_wp_error($terms) ? $terms[0] : null;
                    $category_name = $term ? $term->name : 'Organization';
                    $category_slug = $term ? $term->slug : 'organization';
                    $instrument = get_post_meta($post_id, 'sb_instrument', true) ?: get_post_meta($post_id, 'sb_specialty', true);
                    $description = get_post_meta($post_id, 'sb_description', true) ?: get_the_excerpt();
                    $location = get_post_meta($post_id, 'sb_location', true);
                    $contact = get_post_meta($post_id, 'sb_contact', true);
                    $search_text = strtolower(implode(' ', array(get_the_title(), $description, $instrument, $location, $category_name)));
                ?>
                    <article class="sb-directory-card sb-directory-card--<?php echo esc_attr($category_slug); ?>" data-sb-directory-item data-category="<?php echo esc_attr($category_slug); ?>" data-search="<?php echo esc_attr($search_text); ?>">
                        <div class="sb-directory-card__top">
                            <span class="sb-directory-card__initial" aria-hidden="true"><?php echo esc_html(function_exists('mb_substr') ? mb_substr(get_the_title(), 0, 1) : substr(get_the_title(), 0, 1)); ?></span>
                            <span class="sb-directory-card__category"><?php echo esc_html($category_name); ?></span>
                        </div>
                        <h2><?php the_title(); ?></h2>
                        <?php if ($instrument) : ?><p class="sb-directory-card__instrument"><?php echo esc_html($instrument); ?></p><?php endif; ?>
                        <?php if ($description) : ?><p class="sb-directory-card__description"><?php echo esc_html(wp_trim_words($description, 30, '…')); ?></p><?php endif; ?>
                        <?php if ($location) : ?><p class="sb-directory-card__location"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 5-5.5 10.5-7.4 12.3a.83.83 0 0 1-1.2 0C9.5 20.5 4 15 4 10a8 8 0 1 1 16 0"/><circle cx="12" cy="10" r="3"/></svg><?php echo esc_html($location); ?></p><?php endif; ?>
                        <?php if ($contact) : ?><p class="sb-directory-card__contact"><?php echo esc_html($contact); ?></p><?php endif; ?>
                    </article>
                <?php endwhile; wp_reset_postdata(); ?>
            </div>
            <div class="sb-directory__empty" data-sb-directory-empty hidden>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                <h2>No results found</h2>
                <p>Try a different search term or category.</p>
                <button class="sb-btn" type="button" data-sb-directory-empty-clear>Clear Search</button>
            </div>
            <aside class="sb-directory__callout">
                <div>
                    <?php if (!empty($archive_settings['callout_eyebrow'])) : ?><p class="sb-eyebrow"><?php echo wp_kses_post($archive_settings['callout_eyebrow']); ?></p><?php endif; ?>
                    <h2><?php echo wp_kses_post($archive_settings['callout_heading'] ?? 'Add your listing to the directory'); ?></h2>
                    <?php if (!empty($archive_settings['callout_description'])) : ?><p><?php echo wp_kses_post($archive_settings['callout_description']); ?></p><?php endif; ?>
                </div>
                <?php if (!empty($archive_settings['callout_button_label'])) : ?><a class="sb-btn" href="<?php echo esc_url($archive_settings['callout_button_url'] ?? ''); ?>"><?php echo esc_html($archive_settings['callout_button_label']); ?></a><?php endif; ?>
            </aside>
        </div>
    </section>
</main>

<?php get_footer(); ?>
