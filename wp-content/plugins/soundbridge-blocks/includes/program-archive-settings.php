<?php
/** React-powered Program archive content settings. */

function soundbridge_program_archive_defaults(): array
{
    return array(
        'hero_eyebrow'          => 'Music Education',
        'hero_heading'          => 'Find Your',
        'hero_highlight'        => 'Music Program',
        'hero_description'      => 'We offer programs for every age and experience level — from curious beginners to committed young musicians ready for an intensive experience.',
        'hero_image_id'         => 0,
        'hero_image_url'        => '',
        'callout_eyebrow'       => 'More Coming Soon',
        'callout_heading'       => 'New programs are in development',
        'callout_description'   => 'We’re constantly expanding to serve more students. Sign up for our newsletter or contact us to be first to know about new programs.',
        'callout_primary_label' => 'Get Notified',
        'callout_primary_url'   => '/contact/',
        'callout_second_label'  => 'Scholarship Info',
        'callout_second_url'    => '/scholarships/',
        'cta_eyebrow'           => 'Ready to make music?',
        'cta_heading'           => 'Find your place at SoundBridge.',
        'cta_description'       => 'Registration is open. Scholarships are available — every student deserves access to music education.',
        'cta_button_label'      => 'Apply Now',
        'cta_button_url'        => '/programs/',
    );
}

function soundbridge_get_program_archive_settings(): array
{
    $settings = wp_parse_args((array) get_option('soundbridge_program_archive', array()), soundbridge_program_archive_defaults());
    if (!empty($settings['hero_image_id'])) {
        $settings['hero_image_url'] = wp_get_attachment_image_url($settings['hero_image_id'], 'full') ?: '';
    }
    return $settings;
}

function soundbridge_sanitize_program_archive_settings($input): array
{
    $input = is_array($input) ? $input : array();
    $defaults = soundbridge_program_archive_defaults();
    $output = array();
    $rich_text_fields = array(
        'hero_eyebrow', 'hero_heading', 'hero_highlight', 'hero_description',
        'callout_eyebrow', 'callout_heading', 'callout_description',
        'cta_eyebrow', 'cta_heading', 'cta_description',
    );
    $url_fields = array('callout_primary_url', 'callout_second_url', 'cta_button_url');

    foreach ($defaults as $key => $default) {
        if ('hero_image_url' === $key) continue;
        $value = $input[$key] ?? $default;
        if ('hero_image_id' === $key) {
            $output[$key] = absint($value);
        } elseif (in_array($key, $rich_text_fields, true)) {
            $output[$key] = wp_kses_post($value);
        } elseif (in_array($key, $url_fields, true)) {
            $output[$key] = esc_url_raw($value);
        } else {
            $output[$key] = sanitize_text_field($value);
        }
    }
    return $output;
}

function soundbridge_add_program_archive_settings_page(): void
{
    add_submenu_page(
        'edit.php?post_type=program',
        __('Program Archive Settings', 'soundbridge-blocks'),
        __('Archive Settings', 'soundbridge-blocks'),
        'manage_options',
        'soundbridge-program-archive',
        'soundbridge_render_program_archive_settings_page'
    );
}
add_action('admin_menu', 'soundbridge_add_program_archive_settings_page');

function soundbridge_render_program_archive_settings_page(): void
{
    if (!current_user_can('manage_options')) return;
    echo '<div class="wrap"><div id="soundbridge-program-archive-settings"></div></div>';
}

function soundbridge_program_archive_admin_assets(string $hook): void
{
    if ('program_page_soundbridge-program-archive' !== $hook) return;

    $asset_file = SOUNDBRIDGE_BLOCKS_DIR . 'build/program-archive-settings/index.asset.php';
    $script_file = SOUNDBRIDGE_BLOCKS_DIR . 'build/program-archive-settings/index.js';
    if (!file_exists($asset_file) || !file_exists($script_file)) return;

    $asset = require $asset_file;
    wp_enqueue_media();
    wp_enqueue_script(
        'soundbridge-program-archive-settings',
        plugins_url('build/program-archive-settings/index.js', dirname(__DIR__) . '/soundbridge-blocks.php'),
        $asset['dependencies'],
        $asset['version'],
        true
    );

    $style_file = SOUNDBRIDGE_BLOCKS_DIR . 'build/program-archive-settings/style-index.css';
    if (file_exists($style_file)) {
        wp_enqueue_style('soundbridge-program-archive-settings', plugins_url('build/program-archive-settings/style-index.css', dirname(__DIR__) . '/soundbridge-blocks.php'), array('wp-components'), filemtime($style_file));
    }

    wp_add_inline_script(
        'soundbridge-program-archive-settings',
        'window.soundbridgeProgramArchiveSettings = ' . wp_json_encode(soundbridge_get_program_archive_settings()) . ';',
        'before'
    );
}
add_action('admin_enqueue_scripts', 'soundbridge_program_archive_admin_assets');

function soundbridge_register_program_archive_rest_route(): void
{
    register_rest_route('soundbridge/v1', '/program-archive', array(
        array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => static fn() => rest_ensure_response(soundbridge_get_program_archive_settings()),
            'permission_callback' => static fn() => current_user_can('manage_options'),
        ),
        array(
            'methods'             => WP_REST_Server::EDITABLE,
            'callback'            => function (WP_REST_Request $request) {
                $settings = soundbridge_sanitize_program_archive_settings($request->get_json_params());
                update_option('soundbridge_program_archive', $settings);
                return rest_ensure_response(soundbridge_get_program_archive_settings());
            },
            'permission_callback' => static fn() => current_user_can('manage_options'),
        ),
    ));
}
add_action('rest_api_init', 'soundbridge_register_program_archive_rest_route');
