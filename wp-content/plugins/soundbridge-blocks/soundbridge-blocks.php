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
