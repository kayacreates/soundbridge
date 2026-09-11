<?php
/** React-powered Events archive content settings. */

function soundbridge_event_archive_defaults(): array
{
    return array(
        'hero_eyebrow'         => 'Community Calendar',
        'hero_heading'         => 'Music Happening in',
        'hero_highlight'       => 'Our Community',
        'hero_description'     => 'Concerts, workshops, performances, and registration deadlines — stay connected with everything happening at SoundBridge.',
        'hero_image_id'        => 0,
        'hero_image_url'       => 'https://images.unsplash.com/photo-1563902321212-bd2d1170c543?w=1600&h=700&fit=crop&auto=format',
        'callout_heading'      => 'Stay in the loop',
        'callout_description'  => 'Sign up for our newsletter to receive event announcements, registration reminders, and community news.',
        'callout_button_label' => 'Subscribe to Updates',
        'callout_button_url'   => '/contact/',
    );
}

function soundbridge_get_event_archive_settings(): array
{
    $settings = wp_parse_args((array) get_option('soundbridge_event_archive', array()), soundbridge_event_archive_defaults());
    if (!empty($settings['hero_image_id'])) {
        $settings['hero_image_url'] = wp_get_attachment_image_url($settings['hero_image_id'], 'full') ?: '';
    }
    return $settings;
}

function soundbridge_sanitize_event_archive_settings($input): array
{
    $input = is_array($input) ? $input : array();
    $defaults = soundbridge_event_archive_defaults();
    $output = array();
    $rich_text_fields = array('hero_eyebrow', 'hero_heading', 'hero_highlight', 'hero_description', 'callout_heading', 'callout_description');

    foreach ($defaults as $key => $default) {
        if ('hero_image_url' === $key) continue;
        $value = $input[$key] ?? $default;
        if ('hero_image_id' === $key) {
            $output[$key] = absint($value);
        } elseif (in_array($key, $rich_text_fields, true)) {
            $output[$key] = wp_kses_post($value);
        } elseif ('callout_button_url' === $key) {
            $output[$key] = esc_url_raw($value);
        } else {
            $output[$key] = sanitize_text_field($value);
        }
    }
    return $output;
}

function soundbridge_add_event_archive_settings_page(): void
{
    add_submenu_page(
        'edit.php?post_type=event',
        __('Events Archive Settings', 'soundbridge-blocks'),
        __('Archive Settings', 'soundbridge-blocks'),
        'manage_options',
        'soundbridge-event-archive',
        'soundbridge_render_event_archive_settings_page'
    );
}
add_action('admin_menu', 'soundbridge_add_event_archive_settings_page');

function soundbridge_render_event_archive_settings_page(): void
{
    if (!current_user_can('manage_options')) return;
    echo '<div class="wrap"><div id="soundbridge-event-archive-settings"></div></div>';
}

function soundbridge_event_archive_admin_assets(string $hook): void
{
    if ('event_page_soundbridge-event-archive' !== $hook) return;

    $asset_file = SOUNDBRIDGE_BLOCKS_DIR . 'build/event-archive-settings/index.asset.php';
    $script_file = SOUNDBRIDGE_BLOCKS_DIR . 'build/event-archive-settings/index.js';
    if (!file_exists($asset_file) || !file_exists($script_file)) return;

    $asset = require $asset_file;
    wp_enqueue_media();
    wp_enqueue_script('soundbridge-event-archive-settings', plugins_url('build/event-archive-settings/index.js', dirname(__DIR__) . '/soundbridge-blocks.php'), $asset['dependencies'], $asset['version'], true);

    $style_file = SOUNDBRIDGE_BLOCKS_DIR . 'build/event-archive-settings/style-index.css';
    if (file_exists($style_file)) {
        wp_enqueue_style('soundbridge-event-archive-settings', plugins_url('build/event-archive-settings/style-index.css', dirname(__DIR__) . '/soundbridge-blocks.php'), array('wp-components'), filemtime($style_file));
    }

    wp_add_inline_script('soundbridge-event-archive-settings', 'window.soundbridgeEventArchiveSettings = ' . wp_json_encode(soundbridge_get_event_archive_settings()) . ';', 'before');
}
add_action('admin_enqueue_scripts', 'soundbridge_event_archive_admin_assets');

function soundbridge_register_event_archive_rest_route(): void
{
    register_rest_route('soundbridge/v1', '/event-archive', array(
        array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => static fn() => rest_ensure_response(soundbridge_get_event_archive_settings()),
            'permission_callback' => static fn() => current_user_can('manage_options'),
        ),
        array(
            'methods'             => WP_REST_Server::EDITABLE,
            'callback'            => function (WP_REST_Request $request) {
                $settings = soundbridge_sanitize_event_archive_settings($request->get_json_params());
                update_option('soundbridge_event_archive', $settings);
                return rest_ensure_response(soundbridge_get_event_archive_settings());
            },
            'permission_callback' => static fn() => current_user_can('manage_options'),
        ),
    ));
}
add_action('rest_api_init', 'soundbridge_register_event_archive_rest_route');
