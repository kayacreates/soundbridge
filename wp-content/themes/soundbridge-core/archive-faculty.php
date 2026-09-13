<?php
/** Faculty directory archive. */

get_header();
?>
<main id="primary" class="site-main sb-faculty-archive">
    <nav class="sb-faculty-breadcrumbs" aria-label="Breadcrumb">
        <div class="sb-container"><a href="<?php echo esc_url(home_url('/')); ?>">Home</a><span aria-hidden="true">›</span><span aria-current="page">Meet the Faculty</span></div>
    </nav>
    <header class="sb-faculty-hero">
        <div class="sb-container">
            <p class="sb-eyebrow">Our Educators</p>
            <h1>Meet the Faculty</h1>
            <p>Experienced musicians and educators helping every student grow in skill, confidence, and creativity.</p>
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
</main>
<?php get_footer(); ?>
