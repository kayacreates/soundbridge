<?php
$programs = new WP_Query(array(
    'post_type'      => 'program',
    'posts_per_page' => (int) ($attributes['count'] ?? 6),
));
$background = $attributes['background'] ?? 'pale-blue';
$section_class = 'sb-program-grid-section sb-block-bg alignfull sb-block-bg--' . $background;
$is_editor_preview = !empty($attributes['editorPreview']);
$status_labels = array(
    'open'        => 'Enrolling now',
    'coming-soon' => 'Coming soon',
    'closed'      => 'Registration closed',
);
?>
<?php if (!$is_editor_preview) : ?><section class="<?php echo esc_attr($section_class); ?>"><?php endif; ?>
    <div class="sb-container">
        <?php if (!$is_editor_preview && (!empty($attributes['eyebrow']) || !empty($attributes['heading']) || !empty($attributes['showViewAll']))) : ?>
            <header class="sb-program-grid-section__header">
                <div>
                    <?php if (!empty($attributes['eyebrow'])) : ?><p class="sb-eyebrow"><?php echo esc_html($attributes['eyebrow']); ?></p><?php endif; ?>
                    <?php if (!empty($attributes['heading'])) : ?><h2><?php echo wp_kses_post($attributes['heading']); ?></h2><?php endif; ?>
                </div>
                <?php if (!empty($attributes['showViewAll'])) : ?><a class="sb-btn sb-btn--outline sb-program-grid-section__view-all" href="<?php echo esc_url($attributes['viewAllUrl'] ?? '/programs'); ?>"><?php echo esc_html($attributes['viewAllLabel'] ?? 'View All Programs →'); ?></a><?php endif; ?>
            </header>
        <?php endif; ?>

        <?php if ($programs->have_posts()) : ?>
            <div class="sb-program-grid">
                <?php while ($programs->have_posts()) : $programs->the_post(); ?>
                    <?php
                    $program_id = get_the_ID();
                    $status = get_post_meta($program_id, 'sb_status', true) ?: 'open';
                    $status_label = $status_labels[$status] ?? $status_labels['coming-soon'];
                    $has_scholarship = (bool) get_post_meta($program_id, 'sb_scholarship', true);
                    $registration_url = get_post_meta($program_id, 'sb_registration_url', true) ?: get_permalink();
                    $terms = get_the_terms($program_id, 'program_type');
                    $program_type = $terms && !is_wp_error($terms) ? $terms[0]->name : 'Program';
                    $description = get_post_meta($program_id, 'sb_about', true) ?: get_post_meta($program_id, 'sb_tagline', true) ?: get_the_excerpt();
                    $details = array(
                        'Age'        => get_post_meta($program_id, 'sb_age', true),
                        'Level'      => get_post_meta($program_id, 'sb_level', true),
                        'Instrument' => get_post_meta($program_id, 'sb_instrument', true),
                        'Schedule'   => get_post_meta($program_id, 'sb_schedule', true),
                        'Location'   => get_post_meta($program_id, 'sb_location', true),
                    );
                    ?>
                    <article class="sb-program-card">
                        <div class="sb-program-card__media">
                            <?php if (has_post_thumbnail()) : ?>
                                <?php the_post_thumbnail('large', array('loading' => 'lazy')); ?>
                            <?php endif; ?>
                            <div class="sb-program-card__badges">
                                <span class="sb-program-card__status sb-program-card__status--<?php echo esc_attr($status); ?>"><?php echo esc_html($status_label); ?></span>
                                <?php if ($has_scholarship) : ?><span class="sb-program-card__scholarship">Scholarships Available</span><?php endif; ?>
                            </div>
                        </div>

                        <div class="sb-program-card__content">
                            <p class="sb-program-card__type"><?php echo esc_html($program_type); ?></p>
                            <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                            <?php if ($description) : ?><p class="sb-program-card__description"><?php echo esc_html(wp_trim_words($description, 25, '…')); ?></p><?php endif; ?>

                            <?php if (array_filter($details)) : ?>
                                <dl class="sb-program-card__details">
                                    <?php foreach ($details as $label => $value) : if (!$value) continue; ?>
                                        <div class="<?php echo 'Location' === $label ? 'is-wide' : ''; ?>">
                                            <dt><?php echo esc_html($label); ?></dt>
                                            <dd><?php echo esc_html($value); ?></dd>
                                        </div>
                                    <?php endforeach; ?>
                                </dl>
                            <?php endif; ?>

                            <div class="sb-program-card__actions">
                                <a class="sb-btn sb-btn--outline" href="<?php the_permalink(); ?>">View Program</a>
                                <?php if ('open' === $status) : ?><a class="sb-btn" href="<?php echo esc_url($registration_url); ?>">Register Now</a><?php endif; ?>
                            </div>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
        <?php else : ?>
            <div class="sb-program-grid__empty"><h3>No programs are available yet</h3><p>Please check back soon for new music education opportunities.</p></div>
        <?php endif; ?>
    </div>
<?php if (!$is_editor_preview) : ?></section><?php endif; ?>
<?php wp_reset_postdata(); ?>
