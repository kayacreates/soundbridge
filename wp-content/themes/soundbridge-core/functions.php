<?php
/**
 * SoundBridge Core theme functions.
 *
 * Presentation belongs in this theme. Content models and custom Gutenberg
 * blocks live in the companion SoundBridge Blocks plugin.
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once get_template_directory() . '/inc/setup.php';

/**
 * Display a dashboard notice when the companion plugin is not active.
 */
function soundbridge_core_companion_notice() {
    if (!current_user_can('activate_plugins') || defined('SOUNDBRIDGE_BLOCKS_DIR')) {
        return;
    }

    echo '<div class="notice notice-warning"><p><strong>SoundBridge Core:</strong> Activate the <strong>SoundBridge Blocks</strong> plugin to enable Programs, Events, Music Directory, and custom Gutenberg blocks.</p></div>';
}
add_action('admin_notices', 'soundbridge_core_companion_notice');
