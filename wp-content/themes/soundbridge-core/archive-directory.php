<?php
get_header();

$archive_settings = function_exists('soundbridge_get_directory_archive_settings')
    ? soundbridge_get_directory_archive_settings()
    : array();
$directory = new WP_Query(array(
    'post_type'      => array('directory', 'faculty'),
    'post_status'    => 'publish',
    'posts_per_page' => -1,
    'orderby'        => 'title',
    'order'          => 'ASC',
    'meta_query'     => array(
        'relation' => 'OR',
        array('key' => 'sb_hide_from_directory', 'compare' => 'NOT EXISTS'),
        array('key' => 'sb_hide_from_directory', 'value' => '1', 'compare' => '!='),
    ),
));
$categories = get_terms(array('taxonomy' => 'directory_category', 'hide_empty' => true));
$has_faculty = (bool) array_filter($directory->posts, static fn($item) => 'faculty' === $item->post_type);
$has_teacher_category = !is_wp_error($categories) && (bool) array_filter($categories, static fn($term) => 'teacher' === $term->slug);
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
                    <?php if ($has_faculty && !$has_teacher_category) : ?><button type="button" data-sb-directory-category="teacher">Teacher</button><?php endif; ?>
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
                    $is_faculty = 'faculty' === get_post_type();
                    $terms = $is_faculty ? array() : get_the_terms($post_id, 'directory_category');
                    $term = $terms && !is_wp_error($terms) ? $terms[0] : null;
                    $category_name = $is_faculty ? 'Teacher' : ($term ? $term->name : 'Organization');
                    $category_slug = $is_faculty ? 'teacher' : ($term ? $term->slug : 'organization');
                    $instrument = $is_faculty ? get_post_meta($post_id, 'sb_specialties', true) : (get_post_meta($post_id, 'sb_instrument', true) ?: get_post_meta($post_id, 'sb_specialty', true));
                    $description = $is_faculty ? (get_the_excerpt() ?: wp_trim_words(wp_strip_all_tags(get_the_content()), 30, '…')) : (get_post_meta($post_id, 'sb_description', true) ?: get_the_excerpt());
                    $location = $is_faculty ? '' : get_post_meta($post_id, 'sb_location', true);
                    $contact = $is_faculty ? get_post_meta($post_id, 'sb_email', true) : get_post_meta($post_id, 'sb_contact', true);
                    $faculty_links = $is_faculty ? array(
                        'website' => get_post_meta($post_id, 'sb_website_url', true),
                        'email' => $contact,
                        'instagram' => get_post_meta($post_id, 'sb_instagram_url', true),
                        'facebook' => get_post_meta($post_id, 'sb_facebook_url', true),
                        'youtube' => get_post_meta($post_id, 'sb_youtube_url', true),
                    ) : array();
                    $contact_url = '';
                    $contact_external = false;
                    if (!$is_faculty && $contact) {
                        $trimmed_contact = trim($contact);
                        if (is_email($trimmed_contact)) {
                            $contact_url = 'mailto:' . antispambot($trimmed_contact);
                        } elseif (wp_http_validate_url($trimmed_contact)) {
                            $contact_url = $trimmed_contact;
                            $contact_external = true;
                        } elseif (preg_match('/^(?:www\.)?[a-z0-9.-]+\.[a-z]{2,}(?:\/\S*)?$/i', $trimmed_contact)) {
                            $contact_url = 'https://' . preg_replace('/^www\./i', '', $trimmed_contact);
                            $contact_external = true;
                        } elseif (preg_match('/^[+()\d.\s-]+$/', $trimmed_contact) && strlen(preg_replace('/\D/', '', $trimmed_contact)) >= 7) {
                            $contact_url = 'tel:' . preg_replace('/[^+\d]/', '', $trimmed_contact);
                        } else {
                            $contact_url = home_url('/contact/');
                        }
                    }
                    $search_text = strtolower(implode(' ', array(get_the_title(), $description, $instrument, $location, $category_name)));
                ?>
                    <article class="sb-directory-card sb-directory-card--<?php echo esc_attr($category_slug); ?>" data-sb-directory-item data-category="<?php echo esc_attr($category_slug); ?>" data-search="<?php echo esc_attr($search_text); ?>">
                        <div class="sb-directory-card__top">
                            <?php if ($is_faculty && has_post_thumbnail()) : ?>
                                <a class="sb-directory-card__initial is-image" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr(sprintf(__('View %s’s Faculty profile', 'soundbridge-core'), get_the_title())); ?>"><?php the_post_thumbnail('thumbnail', array('loading' => 'lazy')); ?></a>
                            <?php else : ?>
                                <span class="sb-directory-card__initial" aria-hidden="true"><?php echo esc_html(function_exists('mb_substr') ? mb_substr(get_the_title(), 0, 1) : substr(get_the_title(), 0, 1)); ?></span>
                            <?php endif; ?>
                            <span class="sb-directory-card__category"><?php echo esc_html($category_name); ?></span>
                        </div>
                        <h2><?php if ($is_faculty) : ?><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a><?php else : the_title(); endif; ?></h2>
                        <?php if ($instrument) : ?><p class="sb-directory-card__instrument"><?php echo esc_html($instrument); ?></p><?php endif; ?>
                        <?php if ($description) : ?><p class="sb-directory-card__description"><?php echo esc_html(wp_trim_words($description, 30, '…')); ?></p><?php endif; ?>
                        <?php if ($location) : ?><p class="sb-directory-card__location"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 5-5.5 10.5-7.4 12.3a.83.83 0 0 1-1.2 0C9.5 20.5 4 15 4 10a8 8 0 1 1 16 0"/><circle cx="12" cy="10" r="3"/></svg><?php echo esc_html($location); ?></p><?php endif; ?>
                        <?php if ($is_faculty && array_filter($faculty_links)) : ?>
                            <div class="sb-directory-card__socials" aria-label="<?php echo esc_attr(sprintf(__('%s contact links', 'soundbridge-core'), get_the_title())); ?>">
                                <?php if ($faculty_links['website']) : ?><a href="<?php echo esc_url($faculty_links['website']); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('Visit website', 'soundbridge-core'); ?>"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 0 20M12 2a15.3 15.3 0 0 0 0 20"/></svg></a><?php endif; ?>
                                <?php if ($faculty_links['email']) : ?><a href="mailto:<?php echo esc_attr(antispambot($faculty_links['email'])); ?>" aria-label="<?php esc_attr_e('Send email', 'soundbridge-core'); ?>"><svg viewBox="0 0 24 24" aria-hidden="true"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg></a><?php endif; ?>
                                <?php if ($faculty_links['instagram']) : ?><a href="<?php echo esc_url($faculty_links['instagram']); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('Visit Instagram', 'soundbridge-core'); ?>"><svg viewBox="0 0 24 24" aria-hidden="true"><rect width="20" height="20" x="2" y="2" rx="5"/><path d="M16 11.4A4 4 0 1 1 12.6 8 4 4 0 0 1 16 11.4ZM17.5 6.5h.01"/></svg></a><?php endif; ?>
                                <?php if ($faculty_links['facebook']) : ?><a href="<?php echo esc_url($faculty_links['facebook']); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('Visit Facebook', 'soundbridge-core'); ?>"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3.5l.5-4h-4V7a1 1 0 0 1 1-1h3Z"/></svg></a><?php endif; ?>
                                <?php if ($faculty_links['youtube']) : ?><a href="<?php echo esc_url($faculty_links['youtube']); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e('Visit YouTube', 'soundbridge-core'); ?>"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2.5 7.2A3 3 0 0 1 4.6 5c1.8-.5 5-.5 7.4-.5s5.6 0 7.4.5a3 3 0 0 1 2.1 2.2c.5 1.7.5 3.3.5 4.8s0 3.1-.5 4.8a3 3 0 0 1-2.1 2.2c-1.8.5-5 .5-7.4.5s-5.6 0-7.4-.5a3 3 0 0 1-2.1-2.2C2 15.1 2 13.5 2 12s0-3.1.5-4.8Z"/><path d="m10 9 5 3-5 3Z"/></svg></a><?php endif; ?>
                            </div>
                        <?php elseif ($contact) : ?><p class="sb-directory-card__contact"><a href="<?php echo esc_url($contact_url); ?>"<?php if ($contact_external) : ?> target="_blank" rel="noopener noreferrer"<?php endif; ?>><?php echo esc_html($contact); ?></a></p><?php endif; ?>
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
