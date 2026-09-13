<?php
/**
 * Not-found page template.
 */

defined('ABSPATH') || exit;

get_header();

$programs_url = get_post_type_archive_link('program') ?: home_url('/programs/');
$events_url   = get_post_type_archive_link('event') ?: home_url('/events/');
$directory_url = get_post_type_archive_link('directory') ?: home_url('/music-directory/');
?>

<main id="primary" class="site-main sb-not-found">
    <section class="sb-not-found__hero">
        <div class="sb-container sb-not-found__container">
            <div class="sb-not-found__mark" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 18V5l11-2v13" />
                    <circle cx="6" cy="18" r="3" />
                    <circle cx="17" cy="16" r="3" />
                </svg>
            </div>
            <p class="sb-eyebrow"><?php esc_html_e('Page not found', 'soundbridge-core'); ?></p>
            <p class="sb-not-found__code" aria-hidden="true">404</p>
            <h1><?php esc_html_e('Looks like this page missed a beat.', 'soundbridge-core'); ?></h1>
            <p class="sb-not-found__copy"><?php esc_html_e('The page you’re looking for may have moved, or the link may be out of date. Let’s get you back to the music.', 'soundbridge-core'); ?></p>
            <div class="sb-not-found__actions">
                <a class="sb-btn" href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Back to Home', 'soundbridge-core'); ?> <span aria-hidden="true">→</span></a>
                <a class="sb-btn sb-btn--outline" href="<?php echo esc_url($programs_url); ?>"><?php esc_html_e('Explore Programs', 'soundbridge-core'); ?></a>
            </div>
        </div>
    </section>

    <nav class="sb-not-found__links" aria-label="<?php esc_attr_e('Helpful links', 'soundbridge-core'); ?>">
        <div class="sb-container">
            <p><?php esc_html_e('Or try one of these pages', 'soundbridge-core'); ?></p>
            <ul>
                <li><a href="<?php echo esc_url($programs_url); ?>"><?php esc_html_e('Programs', 'soundbridge-core'); ?> <span aria-hidden="true">→</span></a></li>
                <li><a href="<?php echo esc_url($events_url); ?>"><?php esc_html_e('Events', 'soundbridge-core'); ?> <span aria-hidden="true">→</span></a></li>
                <li><a href="<?php echo esc_url($directory_url); ?>"><?php esc_html_e('Music Directory', 'soundbridge-core'); ?> <span aria-hidden="true">→</span></a></li>
                <li><a href="<?php echo esc_url(home_url('/contact/')); ?>"><?php esc_html_e('Contact Us', 'soundbridge-core'); ?> <span aria-hidden="true">→</span></a></li>
            </ul>
        </div>
    </nav>
</main>

<?php
get_footer();
