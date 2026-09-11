<!doctype html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<header class="sb-header">
    <div class="sb-container sb-header__inner">
        <div class="sb-header__brand">
            <?php if (has_custom_logo()) : ?>
                <?php the_custom_logo(); ?>
            <?php else : ?>
                <a class="sb-logo" href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php echo esc_attr(get_bloginfo('name') . ' — home'); ?>"><?php echo esc_html(get_bloginfo('name')); ?></a>
            <?php endif; ?>
        </div>
        <nav id="primary-menu" class="sb-nav" aria-label="Main">
            <?php wp_nav_menu(array('theme_location' => 'primary', 'container' => false, 'fallback_cb' => false, 'menu_id' => 'desktop-primary-menu')); ?>
        </nav>
        <div class="sb-header__actions">
            <a class="sb-btn sb-btn--outline" href="<?php echo esc_url(home_url('/get-involved/#donate')); ?>">Donate</a>
            <a class="sb-btn" href="<?php echo esc_url(get_post_type_archive_link('program') ?: home_url('/programs/')); ?>">Find a Program</a>
        </div>
        <button class="sb-menu-toggle" type="button" aria-expanded="false" aria-controls="sb-mobile-menu" aria-label="Open menu">
            <svg class="sb-menu-toggle__open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
            <svg class="sb-menu-toggle__close" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="m18 6-12 12M6 6l12 12"/></svg>
        </button>
    </div>
    <div class="sb-mobile-menu" id="sb-mobile-menu" hidden>
        <nav aria-label="Mobile">
            <?php wp_nav_menu(array('theme_location' => 'primary', 'container' => false, 'fallback_cb' => false, 'menu_id' => 'mobile-primary-menu')); ?>
        </nav>
        <div class="sb-mobile-menu__actions">
            <a class="sb-btn sb-btn--outline" href="<?php echo esc_url(home_url('/get-involved/#donate')); ?>">Donate</a>
            <a class="sb-btn" href="<?php echo esc_url(get_post_type_archive_link('program') ?: home_url('/programs/')); ?>">Find a Program</a>
        </div>
    </div>
</header>
<main id="main">
