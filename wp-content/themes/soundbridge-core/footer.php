</main>
<?php
$footer_columns = array(
    'footer_programs' => array('heading' => 'Programs', 'links' => array(
        'The Violin Club' => home_url('/programs/the-violin-club/'),
        'SoundBridge Music Camp' => home_url('/programs/soundbridge-music-camp/'),
        'Scholarships' => home_url('/scholarships/'),
        'All Programs' => get_post_type_archive_link('program') ?: home_url('/programs/'),
    )),
    'footer_about' => array('heading' => 'About', 'links' => array(
        'Our Mission' => home_url('/about/'),
        'Our Story' => home_url('/about/#story'),
        'Community Impact' => home_url('/about/#impact'),
        'Our Team' => home_url('/about/#team'),
    )),
    'footer_involved' => array('heading' => 'Get Involved', 'links' => array(
        'Donate' => home_url('/get-involved/#donate'),
        'Volunteer' => home_url('/get-involved/#volunteer'),
        'Become a Partner' => home_url('/get-involved/#partner'),
        'Sponsor a Program' => home_url('/get-involved/#sponsor'),
    )),
    'footer_explore' => array('heading' => 'Explore', 'links' => array(
        'Events' => get_post_type_archive_link('event') ?: home_url('/events/'),
        'Music Directory' => get_post_type_archive_link('directory') ?: home_url('/music-directory/'),
        'FAQ' => home_url('/faq/'),
        'Contact' => home_url('/contact/'),
    )),
);
$footer_description = get_theme_mod('soundbridge_footer_description', 'Connecting students and families with accessible music education across the Saginaw Bay community.');
$footer_uploads = wp_upload_dir();
$footer_logo_url = get_theme_mod('soundbridge_footer_logo', trailingslashit($footer_uploads['baseurl']) . '2026/09/sblogo_white.png');
$newsletter_form_id = absint(get_theme_mod('soundbridge_footer_newsletter_form_id', 0));
$social_links = array(
    array('label' => 'Facebook', 'url' => get_theme_mod('soundbridge_facebook_url', ''), 'icon' => '<path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3.5l.5-4h-4V7a1 1 0 0 1 1-1h3Z"/>'),
    array('label' => 'Instagram', 'url' => get_theme_mod('soundbridge_instagram_url', ''), 'icon' => '<rect width="20" height="20" x="2" y="2" rx="5"/><path d="M16 11.4A4 4 0 1 1 12.6 8 4 4 0 0 1 16 11.4ZM17.5 6.5h.01"/>'),
    array('label' => 'YouTube', 'url' => get_theme_mod('soundbridge_youtube_url', ''), 'icon' => '<path d="M2.5 7.2A3 3 0 0 1 4.6 5c1.8-.5 5-.5 7.4-.5s5.6 0 7.4.5a3 3 0 0 1 2.1 2.2c.5 1.7.5 3.3.5 4.8s0 3.1-.5 4.8a3 3 0 0 1-2.1 2.2c-1.8.5-5 .5-7.4.5s-5.6 0-7.4-.5a3 3 0 0 1-2.1-2.2C2 15.1 2 13.5 2 12s0-3.1.5-4.8Z"/><path d="m10 9 5 3-5 3Z"/>'),
    array('label' => 'LinkedIn', 'url' => get_theme_mod('soundbridge_linkedin_url', ''), 'icon' => '<path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-4 0v7h-4v-7a6 6 0 0 1 6-6ZM2 9h4v12H2z"/><circle cx="4" cy="4" r="2"/>'),
);
?>
<footer class="sb-footer">
    <div class="sb-container">
        <div class="sb-footer__grid">
            <div class="sb-footer__brand">
                <?php if ($footer_logo_url) : ?>
                    <a class="sb-footer__logo" href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php echo esc_attr(get_bloginfo('name') . ' — home'); ?>">
                        <img src="<?php echo esc_url($footer_logo_url); ?>" alt="<?php echo esc_attr(get_bloginfo('name')); ?>">
                    </a>
                <?php else : ?>
                    <a class="sb-logo sb-logo--light" href="<?php echo esc_url(home_url('/')); ?>"><?php bloginfo('name'); ?></a>
                <?php endif; ?>
                <?php if ($footer_description) : ?><p><?php echo esc_html($footer_description); ?></p><?php endif; ?>
                <?php if (array_filter(array_column($social_links, 'url'))) : ?>
                    <div class="sb-footer__socials">
                        <?php foreach ($social_links as $social) : if (!$social['url']) continue; ?>
                            <a href="<?php echo esc_url($social['url']); ?>" aria-label="<?php echo esc_attr('Follow SoundBridge on ' . $social['label']); ?>" target="_blank" rel="noopener noreferrer"><svg viewBox="0 0 24 24" aria-hidden="true"><?php echo $social['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></svg></a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <nav class="sb-footer__navigation" aria-label="Footer">
                <?php foreach ($footer_columns as $location => $column) : ?>
                    <div class="sb-footer__column">
                        <h2><?php echo esc_html($column['heading']); ?></h2>
                        <?php $menu_location = has_nav_menu($location) ? $location : ('footer_explore' === $location && has_nav_menu('footer') ? 'footer' : ''); ?>
                        <?php if ($menu_location) : ?>
                            <?php wp_nav_menu(array('theme_location' => $menu_location, 'container' => false, 'fallback_cb' => false, 'depth' => 1)); ?>
                        <?php else : ?>
                            <ul><?php foreach ($column['links'] as $label => $url) : ?><li><a href="<?php echo esc_url($url); ?>"><?php echo esc_html($label); ?></a></li><?php endforeach; ?></ul>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </nav>

            <div class="sb-footer__newsletter">
                <h2><?php echo esc_html(get_theme_mod('soundbridge_footer_newsletter_heading', 'Newsletter')); ?></h2>
                <p><?php echo esc_html(get_theme_mod('soundbridge_footer_newsletter_description', 'Program updates, event news, and registration reminders.')); ?></p>
                <?php if ($newsletter_form_id) : ?>
                    <?php echo do_shortcode('[fluentform id="' . $newsletter_form_id . '"]'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                <?php else : ?>
                    <a class="sb-footer__newsletter-link" href="<?php echo esc_url(home_url('/contact/')); ?>">Join our mailing list →</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="sb-footer__bottom">
            <span>© <?php echo esc_html(wp_date('Y')); ?> <?php bloginfo('name'); ?> · 501(c)(3) Nonprofit · Saginaw, Michigan</span>
            <nav aria-label="Legal">
                <?php if (has_nav_menu('footer_legal')) : ?>
                    <?php wp_nav_menu(array('theme_location' => 'footer_legal', 'container' => false, 'fallback_cb' => false, 'depth' => 1)); ?>
                <?php else : ?>
                    <ul><li><a href="<?php echo esc_url(home_url('/privacy-policy/')); ?>">Privacy Policy</a></li><li><a href="<?php echo esc_url(home_url('/terms-of-service/')); ?>">Terms of Service</a></li><li><a href="<?php echo esc_url(home_url('/accessibility/')); ?>">Accessibility</a></li></ul>
                <?php endif; ?>
            </nav>
        </div>
    </div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
