<?php
/** Faculty directory archive. */

get_header();
$archive_settings = function_exists('soundbridge_get_faculty_archive_settings') ? soundbridge_get_faculty_archive_settings() : array();
$hero_image = $archive_settings['hero_image_url'] ?? '';
?>
<main id="primary" class="site-main sb-faculty-archive">
    <nav class="sb-faculty-breadcrumbs" aria-label="Breadcrumb">
        <div class="sb-container"><a href="<?php echo esc_url(home_url('/')); ?>">Home</a><span aria-hidden="true">›</span><span aria-current="page">Meet the Faculty</span></div>
    </nav>
    <header class="sb-faculty-hero<?php echo $hero_image ? ' has-background-image' : ''; ?>"<?php if ($hero_image) : ?> style="--sb-faculty-hero-image: url('<?php echo esc_url($hero_image); ?>');"<?php endif; ?>>
        <div class="sb-container">
            <?php if (!empty($archive_settings['hero_eyebrow'])) : ?><p class="sb-eyebrow"><?php echo wp_kses_post($archive_settings['hero_eyebrow']); ?></p><?php endif; ?>
            <h1><?php echo wp_kses_post($archive_settings['hero_heading'] ?? 'Meet the'); ?><?php if (!empty($archive_settings['hero_highlight'])) : ?> <em class="sb-highlight"><?php echo wp_kses_post($archive_settings['hero_highlight']); ?></em><?php endif; ?></h1>
            <?php if (!empty($archive_settings['hero_description'])) : ?><p><?php echo wp_kses_post($archive_settings['hero_description']); ?></p><?php endif; ?>
        </div>
    </header>
    <section class="sb-faculty-directory">
        <div class="sb-container">
            <?php if (have_posts()) : ?>
                <div class="sb-faculty-grid">
                    <?php while (have_posts()) : the_post();
                        $role = get_post_meta(get_the_ID(), 'sb_role', true);
                        $specialties = get_post_meta(get_the_ID(), 'sb_specialties', true);
                        $intro = get_the_excerpt() ?: wp_trim_words(wp_strip_all_tags(get_the_content()), 28, '…');
                        ?>
                        <article class="sb-faculty-card">
                            <a class="sb-faculty-card__portrait" href="<?php the_permalink(); ?>">
                                <?php if (has_post_thumbnail()) : the_post_thumbnail('medium_large', array('loading' => 'lazy')); else : ?><span aria-hidden="true"><?php echo esc_html(substr(get_the_title(), 0, 1)); ?></span><?php endif; ?>
                            </a>
                            <div class="sb-faculty-card__content">
                                <h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                                <?php if ($role) : ?><p class="sb-faculty-card__role"><?php echo esc_html($role); ?></p><?php endif; ?>
                                <?php if ($specialties) : ?><p class="sb-faculty-card__specialties"><?php echo esc_html($specialties); ?></p><?php endif; ?>
                                <?php if ($intro) : ?><p><?php echo esc_html($intro); ?></p><?php endif; ?>
                                <a class="sb-text-link" href="<?php the_permalink(); ?>">Read full bio →</a>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>
                <?php the_posts_pagination(); ?>
            <?php else : ?>
                <div class="sb-faculty-empty"><h2>Faculty profiles are coming soon.</h2><p>Please check back as we introduce the educators behind SoundBridge programs.</p></div>
            <?php endif; ?>
        </div>
    </section>
    <?php if (!empty($archive_settings['callout_heading'])) : ?>
        <section class="sb-faculty-callout sb-block-bg--pale-blue">
            <div class="sb-container">
                <div>
                    <?php if (!empty($archive_settings['callout_eyebrow'])) : ?><p class="sb-eyebrow"><?php echo wp_kses_post($archive_settings['callout_eyebrow']); ?></p><?php endif; ?>
                    <h2><?php echo wp_kses_post($archive_settings['callout_heading']); ?></h2>
                    <?php if (!empty($archive_settings['callout_description'])) : ?><p><?php echo wp_kses_post($archive_settings['callout_description']); ?></p><?php endif; ?>
                </div>
                <?php if (!empty($archive_settings['callout_button_label'])) : ?><a class="sb-btn" href="<?php echo esc_url($archive_settings['callout_button_url'] ?? ''); ?>"><?php echo esc_html($archive_settings['callout_button_label']); ?></a><?php endif; ?>
            </div>
        </section>
    <?php endif; ?>
</main>
<?php get_footer(); ?>
