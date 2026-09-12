<?php
/** Register post metadata used by SoundBridge content types. */
function soundbridge_register_meta() {
    $program_text_fields = array(
        'tagline', 'structure', 'schedule', 'session_length',
        'location', 'location_detail', 'cost', 'status',
        'registration_label', 'scholarship_label', 'gallery_ids',
    );
    $program_long_fields = array(
        'about', 'learn', 'schedule_details', 'faculty', 'scholarship_detail',
        'what_to_bring', 'gallery_urls', 'testimonials', 'faq',
    );
    $program_url_fields = array('registration_url', 'scholarship_url', 'question_url', 'youtube_url');

    foreach ($program_text_fields as $key) {
        register_post_meta('program', 'sb_' . $key, array(
            'show_in_rest' => true, 'single' => true, 'type' => 'string',
            'sanitize_callback' => 'sanitize_text_field',
            'auth_callback' => static fn() => current_user_can('edit_posts'),
        ));
    }
    foreach ($program_long_fields as $key) {
        register_post_meta('program', 'sb_' . $key, array(
            'show_in_rest' => true, 'single' => true, 'type' => 'string',
            'sanitize_callback' => 'sanitize_textarea_field',
            'auth_callback' => static fn() => current_user_can('edit_posts'),
        ));
    }
    foreach ($program_url_fields as $key) {
        register_post_meta('program', 'sb_' . $key, array(
            'show_in_rest' => true, 'single' => true, 'type' => 'string',
            'sanitize_callback' => 'esc_url_raw',
            'auth_callback' => static fn() => current_user_can('edit_posts'),
        ));
    }
    register_post_meta('program', 'sb_scholarship', array(
        'show_in_rest' => true, 'single' => true, 'type' => 'boolean',
        'sanitize_callback' => 'rest_sanitize_boolean',
        'auth_callback' => static fn() => current_user_can('edit_posts'),
    ));

    $fields = array(
        'event' => array('event_date', 'event_time', 'location', 'address', 'cost', 'audience', 'registration_label'),
        'directory' => array('specialty', 'location', 'contact', 'instrument'),
    );
    foreach ($fields as $type => $keys) {
        foreach ($keys as $key) {
            register_post_meta($type, 'sb_' . $key, array(
                'show_in_rest' => true, 'single' => true, 'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'auth_callback' => static fn() => current_user_can('edit_posts'),
            ));
        }
    }
    register_post_meta('directory', 'sb_description', array(
        'show_in_rest' => true, 'single' => true, 'type' => 'string',
        'sanitize_callback' => 'sanitize_textarea_field',
        'auth_callback' => static fn() => current_user_can('edit_posts'),
    ));
    register_post_meta('event', 'sb_description', array(
        'show_in_rest' => true, 'single' => true, 'type' => 'string',
        'sanitize_callback' => 'sanitize_textarea_field',
        'auth_callback' => static fn() => current_user_can('edit_posts'),
    ));
    register_post_meta('event', 'sb_registration_url', array(
        'show_in_rest' => true, 'single' => true, 'type' => 'string',
        'sanitize_callback' => 'esc_url_raw',
        'auth_callback' => static fn() => current_user_can('edit_posts'),
    ));
}
add_action('init', 'soundbridge_register_meta');

/** Add the structured Program Details editor. */
function soundbridge_add_program_meta_box() {
    add_meta_box('soundbridge-program-details', __('Program Details', 'soundbridge-blocks'), 'soundbridge_render_program_meta_box', 'program', 'normal', 'high');
}
add_action('add_meta_boxes_program', 'soundbridge_add_program_meta_box');

function soundbridge_program_text_field($post_id, $key, $label, $type = 'text', $placeholder = '') {
    $value = get_post_meta($post_id, 'sb_' . $key, true);
    ?>
    <label class="sb-program-field">
        <strong><?php echo esc_html($label); ?></strong>
        <input type="<?php echo esc_attr($type); ?>" name="sb_<?php echo esc_attr($key); ?>" value="<?php echo esc_attr($value); ?>" placeholder="<?php echo esc_attr($placeholder); ?>">
    </label>
    <?php
}

function soundbridge_program_textarea_field($post_id, $key, $label, $description = '', $placeholder = '') {
    $value = get_post_meta($post_id, 'sb_' . $key, true);
    ?>
    <label class="sb-program-field sb-program-field--wide">
        <strong><?php echo esc_html($label); ?></strong>
        <?php if ($description) : ?><span class="description"><?php echo esc_html($description); ?></span><?php endif; ?>
        <textarea name="sb_<?php echo esc_attr($key); ?>" rows="5" placeholder="<?php echo esc_attr($placeholder); ?>"><?php echo esc_textarea($value); ?></textarea>
    </label>
    <?php
}

/** Render all fields needed by the single Program template. */
function soundbridge_render_program_meta_box($post) {
    wp_enqueue_media();
    wp_nonce_field('soundbridge_save_program_meta', 'soundbridge_program_nonce');
    ?>
    <style>
        .sb-program-fields { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 20px; }
        .sb-program-fields h3 { grid-column: 1 / -1; margin: 16px 0 -4px; padding-bottom: 8px; border-bottom: 1px solid #dcdcde; }
        .sb-program-field { display: flex; flex-direction: column; gap: 6px; }
        .sb-program-field--wide { grid-column: 1 / -1; }
        .sb-program-field input, .sb-program-field select, .sb-program-field textarea { width: 100%; }
        .sb-program-field .description { color: #646970; }
        .sb-program-gallery-preview { display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 10px; margin: 4px 0 10px; }
        .sb-program-gallery-preview img { width: 100%; aspect-ratio: 4 / 3; object-fit: cover; border-radius: 4px; }
        .sb-program-gallery-actions { display: flex; gap: 8px; }
        @media (max-width: 782px) { .sb-program-fields { grid-template-columns: 1fr; } .sb-program-field--wide { grid-column: auto; } }
    </style>
    <div class="sb-program-fields">
        <h3><?php esc_html_e('Hero and status', 'soundbridge-blocks'); ?></h3>
        <label class="sb-program-field">
            <strong><?php esc_html_e('Program structure', 'soundbridge-blocks'); ?></strong>
            <?php $structure = get_post_meta($post->ID, 'sb_structure', true) ?: 'standalone'; ?>
            <select name="sb_structure">
                <option value="standalone" <?php selected($structure, 'standalone'); ?>>Standalone program</option>
                <option value="group" <?php selected($structure, 'group'); ?>>Program group</option>
                <option value="option" <?php selected($structure, 'option'); ?>>Program option</option>
            </select>
            <span class="description"><?php esc_html_e('Use Parent in Page Attributes to place an option under a program group.', 'soundbridge-blocks'); ?></span>
        </label>
        <?php soundbridge_program_text_field($post->ID, 'tagline', 'Tagline'); ?>
        <label class="sb-program-field">
            <strong><?php esc_html_e('Status', 'soundbridge-blocks'); ?></strong>
            <?php $status = get_post_meta($post->ID, 'sb_status', true); ?>
            <select name="sb_status">
                <option value="open" <?php selected($status, 'open'); ?>>Enrolling now</option>
                <option value="coming-soon" <?php selected($status, 'coming-soon'); ?>>Coming soon</option>
                <option value="closed" <?php selected($status, 'closed'); ?>>Registration closed</option>
            </select>
        </label>
        <label class="sb-program-field">
            <strong><?php esc_html_e('Scholarship available', 'soundbridge-blocks'); ?></strong>
            <select name="sb_scholarship">
                <option value="0" <?php selected(get_post_meta($post->ID, 'sb_scholarship', true), '0'); ?>>No</option>
                <option value="1" <?php selected(get_post_meta($post->ID, 'sb_scholarship', true), '1'); ?>>Yes</option>
            </select>
        </label>

        <h3><?php esc_html_e('Program overview', 'soundbridge-blocks'); ?></h3>
        <?php soundbridge_program_text_field($post->ID, 'schedule', 'Schedule summary'); ?>
        <?php soundbridge_program_text_field($post->ID, 'session_length', 'Session length'); ?>
        <?php soundbridge_program_text_field($post->ID, 'location', 'Location'); ?>
        <?php soundbridge_program_text_field($post->ID, 'location_detail', 'Location details'); ?>
        <?php soundbridge_program_text_field($post->ID, 'cost', 'Tuition / cost'); ?>
        <?php soundbridge_program_textarea_field($post->ID, 'about', 'About the program'); ?>
        <?php soundbridge_program_textarea_field($post->ID, 'learn', 'What students will learn', 'Enter one item per line.'); ?>

        <h3><?php esc_html_e('Schedule and faculty', 'soundbridge-blocks'); ?></h3>
        <?php soundbridge_program_textarea_field($post->ID, 'schedule_details', 'Detailed schedule', 'One row per line: Day | Time | Description'); ?>
        <?php soundbridge_program_textarea_field($post->ID, 'faculty', 'Faculty and instructors', 'One person per line: Name | Role | Biography'); ?>

        <h3><?php esc_html_e('Pricing and preparation', 'soundbridge-blocks'); ?></h3>
        <?php soundbridge_program_textarea_field($post->ID, 'cost_detail', 'Tuition details'); ?>
        <?php soundbridge_program_textarea_field($post->ID, 'scholarship_detail', 'Scholarship details'); ?>
        <?php soundbridge_program_text_field($post->ID, 'scholarship_label', 'Scholarship link label'); ?>
        <?php soundbridge_program_text_field($post->ID, 'scholarship_url', 'Scholarship URL', 'url'); ?>
        <?php soundbridge_program_textarea_field($post->ID, 'what_to_bring', 'What to bring', 'Enter one item per line.'); ?>

        <h3><?php esc_html_e('Media and social proof', 'soundbridge-blocks'); ?></h3>
        <?php $gallery_ids = array_filter(array_map('absint', explode(',', (string) get_post_meta($post->ID, 'sb_gallery_ids', true)))); ?>
        <div class="sb-program-field sb-program-field--wide">
            <strong><?php esc_html_e('Photo gallery', 'soundbridge-blocks'); ?></strong>
            <span class="description"><?php esc_html_e('Choose multiple images from the WordPress Media Library. The featured image is used as the hero.', 'soundbridge-blocks'); ?></span>
            <input type="hidden" id="sb-gallery-ids" name="sb_gallery_ids" value="<?php echo esc_attr(implode(',', $gallery_ids)); ?>">
            <div class="sb-program-gallery-preview" id="sb-gallery-preview">
                <?php foreach ($gallery_ids as $attachment_id) : echo wp_get_attachment_image($attachment_id, 'thumbnail'); endforeach; ?>
            </div>
            <div class="sb-program-gallery-actions"><button type="button" class="button" id="sb-select-gallery">Choose gallery images</button><button type="button" class="button" id="sb-clear-gallery">Clear gallery</button></div>
        </div>
        <?php soundbridge_program_text_field($post->ID, 'youtube_url', 'YouTube video URL', 'url'); ?>
        <?php soundbridge_program_textarea_field($post->ID, 'testimonials', 'Testimonials', 'One testimonial per line: Quote | Name | Role'); ?>
        <?php soundbridge_program_textarea_field($post->ID, 'faq', 'Frequently asked questions', 'One question per line: Question | Answer'); ?>

        <h3><?php esc_html_e('Calls to action', 'soundbridge-blocks'); ?></h3>
        <?php soundbridge_program_text_field($post->ID, 'registration_label', 'Registration button label', 'text', 'Register Now'); ?>
        <?php soundbridge_program_text_field($post->ID, 'registration_url', 'Registration URL', 'url'); ?>
        <?php soundbridge_program_text_field($post->ID, 'question_url', 'Ask a question URL', 'url'); ?>
    </div>
    <script>
        (() => {
            const selectButton = document.getElementById('sb-select-gallery');
            const clearButton = document.getElementById('sb-clear-gallery');
            const input = document.getElementById('sb-gallery-ids');
            const preview = document.getElementById('sb-gallery-preview');
            if (!selectButton || !window.wp?.media) return;

            selectButton.addEventListener('click', () => {
                const frame = wp.media({ title: 'Choose program gallery images', button: { text: 'Use selected images' }, multiple: true });
                frame.on('open', () => {
                    const selection = frame.state().get('selection');
                    input.value.split(',').filter(Boolean).forEach((id) => {
                        const attachment = wp.media.attachment(Number(id));
                        attachment.fetch();
                        selection.add(attachment);
                    });
                });
                frame.on('select', () => {
                    const images = frame.state().get('selection').toJSON();
                    input.value = images.map((image) => image.id).join(',');
                    preview.innerHTML = images.map((image) => `<img src="${image.sizes?.thumbnail?.url || image.url}" alt="">`).join('');
                });
                frame.open();
            });
            clearButton.addEventListener('click', () => { input.value = ''; preview.innerHTML = ''; });
        })();
    </script>
    <?php
}

/** Save the Program Details fields. */
function soundbridge_save_program_meta($post_id) {
    if (!isset($_POST['soundbridge_program_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['soundbridge_program_nonce'])), 'soundbridge_save_program_meta')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    $textarea_fields = array('about', 'learn', 'schedule_details', 'faculty', 'cost_detail', 'scholarship_detail', 'what_to_bring', 'gallery_urls', 'testimonials', 'faq');
    $url_fields = array('registration_url', 'scholarship_url', 'question_url', 'youtube_url');
    $text_fields = array('tagline', 'structure', 'schedule', 'session_length', 'location', 'location_detail', 'cost', 'status', 'registration_label', 'scholarship_label');

    foreach ($textarea_fields as $key) if (isset($_POST['sb_' . $key])) update_post_meta($post_id, 'sb_' . $key, sanitize_textarea_field(wp_unslash($_POST['sb_' . $key])));
    foreach ($url_fields as $key) if (isset($_POST['sb_' . $key])) update_post_meta($post_id, 'sb_' . $key, esc_url_raw(wp_unslash($_POST['sb_' . $key])));
    foreach ($text_fields as $key) if (isset($_POST['sb_' . $key])) update_post_meta($post_id, 'sb_' . $key, sanitize_text_field(wp_unslash($_POST['sb_' . $key])));
    if (isset($_POST['sb_gallery_ids'])) {
        $gallery_ids = array_filter(array_map('absint', explode(',', sanitize_text_field(wp_unslash($_POST['sb_gallery_ids'])))));
        update_post_meta($post_id, 'sb_gallery_ids', implode(',', $gallery_ids));
    }
    update_post_meta($post_id, 'sb_scholarship', isset($_POST['sb_scholarship']) && '1' === $_POST['sb_scholarship'] ? '1' : '0');
}
add_action('save_post_program', 'soundbridge_save_program_meta');

/** Add the structured Directory Details editor. */
function soundbridge_add_directory_meta_box() {
    add_meta_box(
        'soundbridge-directory-details',
        __('Directory Details', 'soundbridge-blocks'),
        'soundbridge_render_directory_meta_box',
        'directory',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes_directory', 'soundbridge_add_directory_meta_box');

function soundbridge_directory_field($post_id, $key, $label, $placeholder = '') {
    $value = get_post_meta($post_id, 'sb_' . $key, true);
    ?>
    <label class="sb-directory-field">
        <strong><?php echo esc_html($label); ?></strong>
        <input type="text" name="sb_<?php echo esc_attr($key); ?>" value="<?php echo esc_attr($value); ?>" placeholder="<?php echo esc_attr($placeholder); ?>">
    </label>
    <?php
}

function soundbridge_render_directory_meta_box($post) {
    wp_nonce_field('soundbridge_save_directory_meta', 'soundbridge_directory_nonce');
    ?>
    <style>
        .sb-directory-fields { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 20px; }
        .sb-directory-field { display: flex; flex-direction: column; gap: 6px; }
        .sb-directory-field--wide { grid-column: 1 / -1; }
        .sb-directory-field input,
        .sb-directory-field textarea { width: 100%; }
        .sb-directory-field .description { color: #646970; }
        @media (max-width: 782px) { .sb-directory-fields { grid-template-columns: 1fr; } .sb-directory-field--wide { grid-column: auto; } }
    </style>
    <div class="sb-directory-fields">
        <label class="sb-directory-field sb-directory-field--wide">
            <strong><?php esc_html_e('Description', 'soundbridge-blocks'); ?></strong>
            <span class="description"><?php esc_html_e('A short overview shown on the directory card.', 'soundbridge-blocks'); ?></span>
            <textarea name="sb_description" rows="5" placeholder="Describe this teacher, organization, performer, venue, or store."><?php echo esc_textarea(get_post_meta($post->ID, 'sb_description', true)); ?></textarea>
        </label>
        <?php soundbridge_directory_field($post->ID, 'specialty', 'Specialty', 'Private lessons, youth programs, performance space…'); ?>
        <?php soundbridge_directory_field($post->ID, 'instrument', 'Instrument or area', 'Piano, strings, all instruments…'); ?>
        <?php soundbridge_directory_field($post->ID, 'location', 'Location', 'Saginaw, MI'); ?>
        <?php soundbridge_directory_field($post->ID, 'contact', 'Contact information', 'Website, email, phone, or “Contact via SoundBridge”'); ?>
    </div>
    <?php
}

function soundbridge_save_directory_meta($post_id) {
    if (!isset($_POST['soundbridge_directory_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['soundbridge_directory_nonce'])), 'soundbridge_save_directory_meta')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    if (isset($_POST['sb_description'])) {
        update_post_meta($post_id, 'sb_description', sanitize_textarea_field(wp_unslash($_POST['sb_description'])));
    }
    foreach (array('specialty', 'instrument', 'location', 'contact') as $key) {
        if (isset($_POST['sb_' . $key])) {
            update_post_meta($post_id, 'sb_' . $key, sanitize_text_field(wp_unslash($_POST['sb_' . $key])));
        }
    }
}
add_action('save_post_directory', 'soundbridge_save_directory_meta');

/** Add the structured Event Details editor. */
function soundbridge_add_event_meta_box() {
    add_meta_box('soundbridge-event-details', __('Event Details', 'soundbridge-blocks'), 'soundbridge_render_event_meta_box', 'event', 'normal', 'high');
}
add_action('add_meta_boxes_event', 'soundbridge_add_event_meta_box');

function soundbridge_event_field($post_id, $key, $label, $type = 'text', $placeholder = '') {
    $value = get_post_meta($post_id, 'sb_' . $key, true);
    ?>
    <label class="sb-event-field">
        <strong><?php echo esc_html($label); ?></strong>
        <input type="<?php echo esc_attr($type); ?>" name="sb_<?php echo esc_attr($key); ?>" value="<?php echo esc_attr($value); ?>" placeholder="<?php echo esc_attr($placeholder); ?>">
    </label>
    <?php
}

function soundbridge_render_event_meta_box($post) {
    wp_nonce_field('soundbridge_save_event_meta', 'soundbridge_event_nonce');
    ?>
    <style>
        .sb-event-fields { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 20px; }
        .sb-event-fields h3 { grid-column: 1 / -1; margin: 16px 0 -4px; padding-bottom: 8px; border-bottom: 1px solid #dcdcde; }
        .sb-event-field { display: flex; flex-direction: column; gap: 6px; }
        .sb-event-field--wide { grid-column: 1 / -1; }
        .sb-event-field input,
        .sb-event-field textarea { width: 100%; }
        .sb-event-field .description { color: #646970; }
        @media (max-width: 782px) { .sb-event-fields { grid-template-columns: 1fr; } .sb-event-field--wide { grid-column: auto; } }
    </style>
    <div class="sb-event-fields">
        <h3><?php esc_html_e('Event overview', 'soundbridge-blocks'); ?></h3>
        <?php soundbridge_event_field($post->ID, 'event_date', 'Date', 'date'); ?>
        <?php soundbridge_event_field($post->ID, 'event_time', 'Time', 'text', '5:30 PM – 7:00 PM'); ?>
        <label class="sb-event-field sb-event-field--wide">
            <strong><?php esc_html_e('Description', 'soundbridge-blocks'); ?></strong>
            <span class="description"><?php esc_html_e('Used on the event archive card and the About This Event section.', 'soundbridge-blocks'); ?></span>
            <textarea name="sb_description" rows="6" placeholder="Describe the event and what attendees can expect."><?php echo esc_textarea(get_post_meta($post->ID, 'sb_description', true)); ?></textarea>
        </label>

        <h3><?php esc_html_e('Venue and attendance', 'soundbridge-blocks'); ?></h3>
        <?php soundbridge_event_field($post->ID, 'location', 'Venue name', 'text', 'Countryside Trinity Church'); ?>
        <?php soundbridge_event_field($post->ID, 'address', 'Venue address', 'text', '8600 Gratiot Road, Saginaw, MI 48609'); ?>
        <?php soundbridge_event_field($post->ID, 'cost', 'Admission / cost', 'text', 'Free — open to the public'); ?>
        <?php soundbridge_event_field($post->ID, 'audience', 'Audience', 'text', 'Everyone welcome'); ?>

        <h3><?php esc_html_e('Primary action', 'soundbridge-blocks'); ?></h3>
        <?php soundbridge_event_field($post->ID, 'registration_label', 'Button label', 'text', 'Register / Get Tickets'); ?>
        <?php soundbridge_event_field($post->ID, 'registration_url', 'Button URL', 'url'); ?>
    </div>
    <?php
}

function soundbridge_save_event_meta($post_id) {
    if (!isset($_POST['soundbridge_event_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['soundbridge_event_nonce'])), 'soundbridge_save_event_meta')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    if (isset($_POST['sb_description'])) {
        update_post_meta($post_id, 'sb_description', sanitize_textarea_field(wp_unslash($_POST['sb_description'])));
    }
    foreach (array('event_date', 'event_time', 'location', 'address', 'cost', 'audience', 'registration_label') as $key) {
        if (isset($_POST['sb_' . $key])) update_post_meta($post_id, 'sb_' . $key, sanitize_text_field(wp_unslash($_POST['sb_' . $key])));
    }
    if (isset($_POST['sb_registration_url'])) {
        update_post_meta($post_id, 'sb_registration_url', esc_url_raw(wp_unslash($_POST['sb_registration_url'])));
    }
}
add_action('save_post_event', 'soundbridge_save_event_meta');
