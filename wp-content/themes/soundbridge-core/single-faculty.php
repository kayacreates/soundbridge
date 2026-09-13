<?php
/** Individual Faculty biography. */

get_header();

while (have_posts()) : the_post();
    $faculty_id = get_the_ID();
    $role = get_post_meta($faculty_id, 'sb_role', true);
    $specialties = get_post_meta($faculty_id, 'sb_specialties', true);
    $credentials = get_post_meta($faculty_id, 'sb_credentials', true);
    $website_url = get_post_meta($faculty_id, 'sb_website_url', true);
    $youtube_url = get_post_meta($faculty_id, 'sb_youtube_url', true);
    $facebook_url = get_post_meta($faculty_id, 'sb_facebook_url', true);
    $instagram_url = get_post_meta($faculty_id, 'sb_instagram_url', true);
    $email = get_post_meta($faculty_id, 'sb_email', true);
    $programs = new WP_Query(array(
        'post_type' => 'program',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'meta_query' => array(array(
            'key' => 'sb_faculty_ids',
            'value' => '(^|,)' . $faculty_id . '(,|$)',
            'compare' => 'REGEXP',
        )),
    ));
    ?>
    <main id="primary" class="site-main sb-faculty-profile">
        <nav class="sb-faculty-breadcrumbs" aria-label="Breadcrumb">
            <div class="sb-container"><a href="<?php echo esc_url(home_url('/')); ?>">Home</a><span aria-hidden="true">›</span><a href="<?php echo esc_url(get_post_type_archive_link('faculty')); ?>">Meet the Faculty</a><span aria-hidden="true">›</span><span aria-current="page"><?php the_title(); ?></span></div>
        </nav>
        <header class="sb-faculty-profile__hero">
            <div class="sb-container">
                <div class="sb-faculty-profile__portrait">
                    <?php if (has_post_thumbnail()) : the_post_thumbnail('large'); else : ?><span aria-hidden="true"><?php echo esc_html(substr(get_the_title(), 0, 1)); ?></span><?php endif; ?>
                </div>
                <div>
                    <p class="sb-eyebrow">Faculty</p>
                    <h1><?php the_title(); ?></h1>
                    <?php if ($role) : ?><p class="sb-faculty-profile__role"><?php echo esc_html($role); ?></p><?php endif; ?>
                    <?php if ($specialties) : ?><p class="sb-faculty-profile__specialties"><?php echo esc_html($specialties); ?></p><?php endif; ?>
                </div>
            </div>
        </header>
        <div class="sb-container sb-faculty-profile__layout">
            <article class="sb-faculty-profile__bio">
                <h2>About <?php the_title(); ?></h2>
                <?php the_content(); ?>
            </article>
            <aside>
                <?php if ($credentials || $specialties || $website_url || $youtube_url || $facebook_url || $instagram_url || $email) : ?>
                    <div class="sb-faculty-profile__details">
                        <h2>Faculty Details</h2>
                        <?php if ($specialties) : ?><div><span>Specialties</span><strong><?php echo esc_html($specialties); ?></strong></div><?php endif; ?>
                        <?php if ($credentials) : ?><div><span>Credentials</span><strong><?php echo esc_html($credentials); ?></strong></div><?php endif; ?>
                        <div class="sb-faculty-profile__links">
                            <?php if ($website_url) : ?><a href="<?php echo esc_url($website_url); ?>" target="_blank" rel="noopener noreferrer" aria-label="Visit website"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 0 20M12 2a15.3 15.3 0 0 0 0 20"/></svg></a><?php endif; ?>
                            <?php if ($youtube_url) : ?><a href="<?php echo esc_url($youtube_url); ?>" target="_blank" rel="noopener noreferrer" aria-label="Visit YouTube"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2.5 7.2A3 3 0 0 1 4.6 5c1.8-.5 5-.5 7.4-.5s5.6 0 7.4.5a3 3 0 0 1 2.1 2.2c.5 1.7.5 3.3.5 4.8s0 3.1-.5 4.8a3 3 0 0 1-2.1 2.2c-1.8.5-5 .5-7.4.5s-5.6 0-7.4-.5a3 3 0 0 1-2.1-2.2C2 15.1 2 13.5 2 12s0-3.1.5-4.8Z"/><path d="m10 9 5 3-5 3Z"/></svg></a><?php endif; ?>
                            <?php if ($facebook_url) : ?><a href="<?php echo esc_url($facebook_url); ?>" target="_blank" rel="noopener noreferrer" aria-label="Visit Facebook"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3.5l.5-4h-4V7a1 1 0 0 1 1-1h3Z"/></svg></a><?php endif; ?>
                            <?php if ($instagram_url) : ?><a href="<?php echo esc_url($instagram_url); ?>" target="_blank" rel="noopener noreferrer" aria-label="Visit Instagram"><svg viewBox="0 0 24 24" aria-hidden="true"><rect width="20" height="20" x="2" y="2" rx="5"/><path d="M16 11.4A4 4 0 1 1 12.6 8 4 4 0 0 1 16 11.4ZM17.5 6.5h.01"/></svg></a><?php endif; ?>
                            <?php if ($email) : ?><a href="mailto:<?php echo esc_attr(antispambot($email)); ?>" aria-label="Send email"><svg viewBox="0 0 24 24" aria-hidden="true"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg></a><?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if ($programs->have_posts()) : ?>
                    <div class="sb-faculty-profile__programs"><h2>Programs</h2><?php while ($programs->have_posts()) : $programs->the_post(); ?><a href="<?php the_permalink(); ?>"><?php the_title(); ?><span aria-hidden="true">→</span></a><?php endwhile; ?></div>
                <?php endif; wp_reset_postdata(); ?>
            </aside>
        </div>
    </main>
    <?php
endwhile;

get_footer();
