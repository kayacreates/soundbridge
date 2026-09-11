<?php
/** React-powered Music Directory archive content settings. */

function soundbridge_directory_archive_defaults(): array
{
    return array(
        'hero_eyebrow'        => 'Community Resource',
        'hero_heading'        => 'Saginaw Bay',
        'hero_highlight'      => 'Music Directory',
        'hero_description'    => 'Discover local music teachers, organizations, venues, stores, and performers in the Saginaw Bay area.',
        'hero_image_id'       => 0,
        'hero_image_url'      => 'https://images.unsplash.com/photo-1510915361894-db8b60106cb1?w=1600&h=700&fit=crop&auto=format',
        'callout_eyebrow'     => 'Are you a local music professional?',
        'callout_heading'     => 'Add your listing to the directory',
        'callout_description' => 'We welcome musicians, teachers, venues, and music businesses that serve the Saginaw Bay community. Listings are free.',
        'callout_button_label'=> 'Request a Listing',
        'callout_button_url'  => 'mailto:info@saginawbaysoundbridge.com?subject=Directory%20Listing%20Request',
    );
}

function soundbridge_get_directory_archive_settings(): array
{
    $settings = wp_parse_args((array) get_option('soundbridge_directory_archive', array()), soundbridge_directory_archive_defaults());
    if (!empty($settings['hero_image_id'])) {
        $settings['hero_image_url'] = wp_get_attachment_image_url($settings['hero_image_id'], 'full') ?: '';
    }
    return $settings;
}

function soundbridge_sanitize_directory_archive_settings($input): array
{
    $input = is_array($input) ? $input : array();
    $defaults = soundbridge_directory_archive_defaults();
    $output = array();
    $rich_text_fields = array('hero_eyebrow', 'hero_heading', 'hero_highlight', 'hero_description', 'callout_eyebrow', 'callout_heading', 'callout_description');

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

function soundbridge_add_directory_archive_settings_page(): void
{
    add_submenu_page(
        'edit.php?post_type=directory',
        __('Music Directory Archive Settings', 'soundbridge-blocks'),
        __('Archive Settings', 'soundbridge-blocks'),
        'manage_options',
        'soundbridge-directory-archive',
        'soundbridge_render_directory_archive_settings_page'
    );
}
add_action('admin_menu', 'soundbridge_add_directory_archive_settings_page');

function soundbridge_render_directory_archive_settings_page(): void
{
    if (!current_user_can('manage_options')) return;
    echo '<div class="wrap"><div id="soundbridge-directory-archive-settings"></div></div>';
}

function soundbridge_directory_archive_admin_assets(string $hook): void
{
    if ('directory_page_soundbridge-directory-archive' !== $hook) return;

    $asset_file = SOUNDBRIDGE_BLOCKS_DIR . 'build/directory-archive-settings/index.asset.php';
    $script_file = SOUNDBRIDGE_BLOCKS_DIR . 'build/directory-archive-settings/index.js';
    if (!file_exists($asset_file) || !file_exists($script_file)) return;

    $asset = require $asset_file;
    wp_enqueue_media();
    wp_enqueue_script('soundbridge-directory-archive-settings', plugins_url('build/directory-archive-settings/index.js', dirname(__DIR__) . '/soundbridge-blocks.php'), $asset['dependencies'], $asset['version'], true);

    $style_file = SOUNDBRIDGE_BLOCKS_DIR . 'build/directory-archive-settings/style-index.css';
    if (file_exists($style_file)) {
        wp_enqueue_style('soundbridge-directory-archive-settings', plugins_url('build/directory-archive-settings/style-index.css', dirname(__DIR__) . '/soundbridge-blocks.php'), array('wp-components'), filemtime($style_file));
    }

    wp_add_inline_script('soundbridge-directory-archive-settings', 'window.soundbridgeDirectoryArchiveSettings = ' . wp_json_encode(soundbridge_get_directory_archive_settings()) . ';', 'before');
}
add_action('admin_enqueue_scripts', 'soundbridge_directory_archive_admin_assets');

function soundbridge_register_directory_archive_rest_route(): void
{
    register_rest_route('soundbridge/v1', '/directory-archive', array(
        array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => static fn() => rest_ensure_response(soundbridge_get_directory_archive_settings()),
            'permission_callback' => static fn() => current_user_can('manage_options'),
        ),
        array(
            'methods'             => WP_REST_Server::EDITABLE,
            'callback'            => function (WP_REST_Request $request) {
                $settings = soundbridge_sanitize_directory_archive_settings($request->get_json_params());
                update_option('soundbridge_directory_archive', $settings);
                return rest_ensure_response(soundbridge_get_directory_archive_settings());
            },
            'permission_callback' => static fn() => current_user_can('manage_options'),
        ),
    ));
}
add_action('rest_api_init', 'soundbridge_register_directory_archive_rest_route');
