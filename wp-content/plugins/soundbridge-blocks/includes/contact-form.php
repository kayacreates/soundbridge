<?php
defined('ABSPATH') || exit;

function soundbridge_handle_contact_form(): void
{
    $redirect = wp_get_referer() ?: home_url('/contact/');
    $redirect = remove_query_arg('sb_contact', $redirect);

    if (!isset($_POST['soundbridge_contact_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['soundbridge_contact_nonce'])), 'soundbridge_contact_form')) {
        wp_safe_redirect(add_query_arg('sb_contact', 'error', $redirect));
        exit;
    }

    if (!empty($_POST['website_check'])) {
        wp_safe_redirect(add_query_arg('sb_contact', 'success', $redirect));
        exit;
    }

    $name    = sanitize_text_field(wp_unslash($_POST['contact_name'] ?? ''));
    $email   = sanitize_email(wp_unslash($_POST['contact_email'] ?? ''));
    $subject = sanitize_text_field(wp_unslash($_POST['contact_subject'] ?? ''));
    $message = sanitize_textarea_field(wp_unslash($_POST['contact_message'] ?? ''));
    $to      = sanitize_email(get_option('admin_email'));

    if (!$name || !is_email($email) || !$subject || mb_strlen($message) < 10 || !is_email($to)) {
        wp_safe_redirect(add_query_arg('sb_contact', 'error', $redirect));
        exit;
    }

    $sent = wp_mail($to, sprintf('[SoundBridge Contact] %s', $subject), "Name: {$name}\nEmail: {$email}\nSubject: {$subject}\n\n{$message}", ['Reply-To: ' . $name . ' <' . $email . '>']);
    wp_safe_redirect(add_query_arg('sb_contact', $sent ? 'success' : 'error', $redirect));
    exit;
}
add_action('admin_post_nopriv_soundbridge_contact_form', 'soundbridge_handle_contact_form');
add_action('admin_post_soundbridge_contact_form', 'soundbridge_handle_contact_form');
