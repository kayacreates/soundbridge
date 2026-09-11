<?php
/** @var array $attributes Block attributes. */
$background    = $attributes['background'] ?? 'white';
$section_class = 'sb-contact sb-block-bg alignfull' . ($background !== 'white' ? ' sb-block-bg--' . $background : '');
$status        = isset($_GET['sb_contact']) ? sanitize_key(wp_unslash($_GET['sb_contact'])) : '';
$subjects      = ['General Inquiry', 'Program Registration', 'Scholarship Application', 'Volunteering', 'Donation / Giving', 'Partnership Inquiry', 'Faculty / Teaching', 'Sponsorship', 'Media / Press', 'Other'];
$quick_links   = is_array($attributes['quickLinks'] ?? null) ? $attributes['quickLinks'] : [];
$fluent_form_id = absint($attributes['fluentFormId'] ?? 0);
?>
<section <?php echo get_block_wrapper_attributes(['class' => $section_class]); ?>>
  <div class="sb-container sb-contact__grid">
    <div>
      <?php if (!empty($attributes['eyebrow'])) : ?><p class="sb-eyebrow"><?php echo esc_html($attributes['eyebrow']); ?></p><?php endif; ?>
      <h2><?php echo wp_kses_post($attributes['heading'] ?? "We're here to help."); ?></h2>
      <?php if ($status === 'success') : ?>
        <div class="sb-contact__notice sb-contact__notice--success" role="status"><h3>Message sent!</h3><p>Thank you for reaching out. We'll be in touch within one business day.</p></div>
      <?php elseif ($status === 'error') : ?>
        <div class="sb-contact__notice sb-contact__notice--error" role="alert"><h3>We couldn't send your message.</h3><p>Please check the form and try again, or email us directly.</p></div>
      <?php endif; ?>
      <?php if ($fluent_form_id && shortcode_exists('fluentform')) : ?>
        <div class="sb-contact__form sb-contact__form--fluent">
          <?php echo do_shortcode('[fluentform id="' . $fluent_form_id . '"]'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Fluent Forms owns and escapes its shortcode output. ?>
        </div>
      <?php else : ?>
        <form class="sb-contact__form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="action" value="soundbridge_contact_form">
        <?php wp_nonce_field('soundbridge_contact_form', 'soundbridge_contact_nonce'); ?>
        <label class="sb-contact__honeypot" aria-hidden="true">Website<input type="text" name="website_check" tabindex="-1" autocomplete="off"></label>
        <div class="sb-contact__row"><label>Full Name <span>*</span><input type="text" name="contact_name" autocomplete="name" required placeholder="Your name"></label><label>Email Address <span>*</span><input type="email" name="contact_email" autocomplete="email" required placeholder="you@example.com"></label></div>
        <label>Subject <span>*</span><select name="contact_subject" required><option value="">Select a topic…</option><?php foreach ($subjects as $subject) : ?><option value="<?php echo esc_attr($subject); ?>"><?php echo esc_html($subject); ?></option><?php endforeach; ?></select></label>
        <label>Message <span>*</span><textarea name="contact_message" rows="6" minlength="10" required placeholder="How can we help you?"></textarea></label>
        <button class="sb-btn" type="submit"><?php echo esc_html($attributes['submitLabel'] ?? 'Send Message →'); ?></button>
        </form>
      <?php endif; ?>
    </div>
    <aside class="sb-contact__sidebar">
      <div class="sb-contact__info">
        <h3>Contact Information</h3>
        <div class="sb-contact__info-row">
          <svg class="sb-contact__info-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
          <div><strong>Email</strong><a href="mailto:<?php echo esc_attr($attributes['email'] ?? ''); ?>"><?php echo esc_html($attributes['email'] ?? ''); ?></a></div>
        </div>
        <div class="sb-contact__info-row">
          <svg class="sb-contact__info-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 0 20M12 2a15.3 15.3 0 0 0 0 20"/></svg>
          <div><strong>Website</strong><a href="<?php echo esc_url($attributes['websiteUrl'] ?? ''); ?>"><?php echo esc_html($attributes['websiteLabel'] ?? ''); ?></a></div>
        </div>
        <div class="sb-contact__info-row">
          <svg class="sb-contact__info-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 10c0 5-5.5 10.5-7.4 12.3a.83.83 0 0 1-1.2 0C9.5 20.5 4 15 4 10a8 8 0 1 1 16 0"/><circle cx="12" cy="10" r="3"/></svg>
          <div><strong>Primary Venue</strong><span><?php echo nl2br(esc_html($attributes['venue'] ?? '')); ?></span></div>
        </div>
      </div>
      <div class="sb-contact__response"><h3>Response Time</h3><p>⚡ <?php echo esc_html($attributes['responseText'] ?? ''); ?></p></div>
      <div class="sb-contact__quick-links"><h3>Quick Links</h3><?php foreach ($quick_links as $link) : ?><a href="<?php echo esc_url($link['url'] ?? ''); ?>">→ <?php echo esc_html($link['label'] ?? ''); ?></a><?php endforeach; ?></div>
    </aside>
  </div>
</section>
