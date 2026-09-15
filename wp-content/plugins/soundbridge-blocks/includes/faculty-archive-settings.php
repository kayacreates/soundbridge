<?php
/** React-powered Faculty archive content settings. */

function soundbridge_faculty_archive_defaults(): array
{
    return array(
        'hero_eyebrow'         => 'Our Educators',
        'hero_heading'         => 'Meet the',
        'hero_highlight'       => 'Faculty',
        'hero_description'     => 'Experienced musicians and educators helping every student grow in skill, confidence, and creativity.',
        'hero_image_id'        => 0,
        'hero_image_url'       => '',
        'callout_eyebrow'      => 'Join Our Team',
        'callout_heading'      => 'Help inspire the next generation of musicians',
        'callout_description'  => 'SoundBridge welcomes experienced musicians and educators who share our commitment to accessible music education.',
        'callout_button_label' => 'Contact Us',
        'callout_button_url'   => '/contact/',
    );
}

function soundbridge_get_faculty_archive_settings(): array
{
    $settings = wp_parse_args((array) get_option('soundbridge_faculty_archive', array()), soundbridge_faculty_archive_defaults());
    if (!empty($settings['hero_image_id'])) {
        $settings['hero_image_url'] = wp_get_attachment_image_url($settings['hero_image_id'], 'full') ?: '';
    }
    return $settings;
}

function soundbridge_sanitize_faculty_archive_settings($input): array
{
    $input = is_array($input) ? $input : array();
    $output = array();
    $rich_text_fields = array('hero_eyebrow', 'hero_heading', 'hero_highlight', 'hero_description', 'callout_eyebrow', 'callout_heading', 'callout_description');

    foreach (soundbridge_faculty_archive_defaults() as $key => $default) {
        if ('hero_image_url' === $key) continue;
        $value = $input[$key] ?? $default;
        if ('hero_image_id' === $key) $output[$key] = absint($value);
        elseif (in_array($key, $rich_text_fields, true)) $output[$key] = wp_kses_post($value);
        elseif ('callout_button_url' === $key) $output[$key] = esc_url_raw($value);
        else $output[$key] = sanitize_text_field($value);
    }
    return $output;
}

function soundbridge_add_faculty_archive_settings_page(): void
{
    add_submenu_page('edit.php?post_type=faculty', __('Faculty Archive Settings', 'soundbridge-blocks'), __('Archive Settings', 'soundbridge-blocks'), 'manage_options', 'soundbridge-faculty-archive', 'soundbridge_render_faculty_archive_settings_page');
}
add_action('admin_menu', 'soundbridge_add_faculty_archive_settings_page');

function soundbridge_render_faculty_archive_settings_page(): void
{
    if (!current_user_can('manage_options')) return;
    echo '<div class="wrap"><div id="soundbridge-faculty-archive-settings"></div></div>';
}

function soundbridge_faculty_archive_admin_assets(string $hook): void
{
    if ('faculty_page_soundbridge-faculty-archive' !== $hook) return;
    $asset_file = SOUNDBRIDGE_BLOCKS_DIR . 'build/faculty-archive-settings/index.asset.php';
    $script_file = SOUNDBRIDGE_BLOCKS_DIR . 'build/faculty-archive-settings/index.js';
    if (!file_exists($asset_file) || !file_exists($script_file)) return;

    $asset = require $asset_file;
    wp_enqueue_media();
    wp_enqueue_script('soundbridge-faculty-archive-settings', plugins_url('build/faculty-archive-settings/index.js', dirname(__DIR__) . '/soundbridge-blocks.php'), $asset['dependencies'], $asset['version'], true);
    $style_file = SOUNDBRIDGE_BLOCKS_DIR . 'build/faculty-archive-settings/style-index.css';
    if (file_exists($style_file)) wp_enqueue_style('soundbridge-faculty-archive-settings', plugins_url('build/faculty-archive-settings/style-index.css', dirname(__DIR__) . '/soundbridge-blocks.php'), array('wp-components'), filemtime($style_file));
    wp_add_inline_script('soundbridge-faculty-archive-settings', 'window.soundbridgeFacultyArchiveSettings = ' . wp_json_encode(soundbridge_get_faculty_archive_settings()) . ';', 'before');
}
add_action('admin_enqueue_scripts', 'soundbridge_faculty_archive_admin_assets');

function soundbridge_register_faculty_archive_rest_route(): void
{
    register_rest_route('soundbridge/v1', '/faculty-archive', array(
        array('methods' => WP_REST_Server::READABLE, 'callback' => static fn() => rest_ensure_response(soundbridge_get_faculty_archive_settings()), 'permission_callback' => static fn() => current_user_can('manage_options')),
        array('methods' => WP_REST_Server::EDITABLE, 'callback' => function (WP_REST_Request $request) {
            $settings = soundbridge_sanitize_faculty_archive_settings($request->get_json_params());
            update_option('soundbridge_faculty_archive', $settings);
            return rest_ensure_response(soundbridge_get_faculty_archive_settings());
        }, 'permission_callback' => static fn() => current_user_can('manage_options')),
    ));
}
add_action('rest_api_init', 'soundbridge_register_faculty_archive_rest_route');
