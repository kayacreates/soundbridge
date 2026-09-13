<?php
/**
 * Plugin Name: SoundBridge Blocks
 * Description: Content models and custom Gutenberg blocks for the SoundBridge website.
 * Version: 1.0.0
 * Requires at least: 6.4
 * Requires PHP: 8.0
 * Author: SoundBridge
 * Text Domain: soundbridge-blocks
 */

defined('ABSPATH') || exit;

define('SOUNDBRIDGE_BLOCKS_DIR', plugin_dir_path(__FILE__));

require_once SOUNDBRIDGE_BLOCKS_DIR . 'includes/post-types.php';
require_once SOUNDBRIDGE_BLOCKS_DIR . 'includes/meta.php';
require_once SOUNDBRIDGE_BLOCKS_DIR . 'includes/contact-form.php';
require_once SOUNDBRIDGE_BLOCKS_DIR . 'includes/program-archive-settings.php';
require_once SOUNDBRIDGE_BLOCKS_DIR . 'includes/directory-archive-settings.php';
require_once SOUNDBRIDGE_BLOCKS_DIR . 'includes/event-archive-settings.php';
require_once SOUNDBRIDGE_BLOCKS_DIR . 'includes/fluent-forms.php';

/** Register every compiled SoundBridge block that has block metadata. */
function soundbridge_blocks_register_blocks(): void
{
    $block_directories = glob(
        SOUNDBRIDGE_BLOCKS_DIR . 'build/*',
        GLOB_ONLYDIR
    );

    if (!is_array($block_directories)) {
        return;
    }

    foreach ($block_directories as $block_directory) {
        if (file_exists($block_directory . '/block.json')) {
            register_block_type($block_directory);
        }
    }
}
add_action('init', 'soundbridge_blocks_register_blocks');

/** Expose SoundBridge typography as styles for compatible core blocks. */
function soundbridge_blocks_register_core_block_styles(): void
{
    register_block_style(
        'core/paragraph',
        [
            'name'  => 'sb-eyebrow',
            'label' => __('Eyebrow', 'soundbridge-blocks'),
        ]
    );
}
add_action('init', 'soundbridge_blocks_register_core_block_styles');

/** Load assets shared by every SoundBridge block, including inside the editor iframe. */
function soundbridge_blocks_enqueue_shared_assets(): void
{
    wp_enqueue_style('dashicons');

    $background_stylesheet = SOUNDBRIDGE_BLOCKS_DIR . 'build/section-background.css';

    if (file_exists($background_stylesheet)) {
        wp_enqueue_style(
            'soundbridge-block-backgrounds',
            plugins_url('build/section-background.css', __FILE__),
            [],
            (string) filemtime($background_stylesheet)
        );
    }
}
add_action('enqueue_block_assets', 'soundbridge_blocks_enqueue_shared_assets');

/** Add a dedicated block-inserter category. */
function soundbridge_blocks_category(array $categories): array
{
    return array_merge(
        [
            [
                'slug'  => 'soundbridge',
                'title' => __('SoundBridge', 'soundbridge-blocks'),
                'icon'  => 'admin-customizer',
            ],
        ],
        $categories
    );
}
add_filter('block_categories_all', 'soundbridge_blocks_category');

/** Register content types before rewrite rules are flushed on activation. */
function soundbridge_blocks_activate(): void
{
    soundbridge_register_post_types();
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'soundbridge_blocks_activate');

/** Flush plugin rewrite rules on deactivation. */
function soundbridge_blocks_deactivate(): void
{
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'soundbridge_blocks_deactivate');
