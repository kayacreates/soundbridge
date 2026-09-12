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
    'f' => array('label' => 'Facebook', 'url' => get_theme_mod('soundbridge_facebook_url', '')),
    'ig' => array('label' => 'Instagram', 'url' => get_theme_mod('soundbridge_instagram_url', '')),
    'yt' => array('label' => 'YouTube', 'url' => get_theme_mod('soundbridge_youtube_url', '')),
    'in' => array('label' => 'LinkedIn', 'url' => get_theme_mod('soundbridge_linkedin_url', '')),
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
                        <?php foreach ($social_links as $short_label => $social) : if (!$social['url']) continue; ?>
                            <a href="<?php echo esc_url($social['url']); ?>" aria-label="<?php echo esc_attr('Follow SoundBridge on ' . $social['label']); ?>" target="_blank" rel="noopener noreferrer"><?php echo esc_html($short_label); ?></a>
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
