<?php
$count = max(1, min(12, (int) ($attributes['count'] ?? 4)));
$columns = max(2, min(4, (int) ($attributes['columns'] ?? 4)));
$background = $attributes['background'] ?? 'white';
$is_editor_preview = !empty($attributes['editorPreview']);
$faculty = new WP_Query(array(
    'post_type' => 'faculty',
    'post_status' => 'publish',
    'posts_per_page' => $count,
    'orderby' => array('menu_order' => 'ASC', 'title' => 'ASC'),
));
$section_class = 'sb-faculty-grid-section sb-block-bg alignfull sb-block-bg--' . $background;
?>
<?php if (!$is_editor_preview) : ?><section <?php echo get_block_wrapper_attributes(array('class' => $section_class)); ?>><?php endif; ?>
    <div class="sb-container">
        <?php if (!$is_editor_preview) : ?>
            <header class="sb-faculty-grid-section__header">
                <div>
                    <?php if (!empty($attributes['eyebrow'])) : ?><p class="sb-eyebrow"><?php echo esc_html($attributes['eyebrow']); ?></p><?php endif; ?>
                    <?php if (!empty($attributes['heading'])) : ?><h2><?php echo wp_kses_post($attributes['heading']); ?></h2><?php endif; ?>
                </div>
                <?php if (!empty($attributes['showViewAll'])) : ?><a class="sb-btn sb-btn--outline" href="<?php echo esc_url($attributes['viewAllUrl'] ?? get_post_type_archive_link('faculty')); ?>"><?php echo esc_html($attributes['viewAllLabel'] ?? 'Meet All Faculty →'); ?></a><?php endif; ?>
            </header>
        <?php endif; ?>
        <?php if ($faculty->have_posts()) : ?>
            <div class="sb-faculty-grid-block sb-faculty-grid-block--<?php echo esc_attr($columns); ?>">
                <?php while ($faculty->have_posts()) : $faculty->the_post();
                    $role = get_post_meta(get_the_ID(), 'sb_role', true);
                    $intro = get_the_excerpt() ?: wp_trim_words(wp_strip_all_tags(get_the_content()), 18, '…');
                    ?>
                    <article class="sb-faculty-grid-card">
                        <a class="sb-faculty-grid-card__portrait" href="<?php the_permalink(); ?>"><?php if (has_post_thumbnail()) : the_post_thumbnail('medium_large', array('loading' => 'lazy')); else : ?><span aria-hidden="true"><?php echo esc_html(substr(get_the_title(), 0, 1)); ?></span><?php endif; ?></a>
                        <div><h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3><?php if ($role) : ?><strong><?php echo esc_html($role); ?></strong><?php endif; ?><?php if ($intro) : ?><p><?php echo esc_html($intro); ?></p><?php endif; ?><a class="sb-text-link" href="<?php the_permalink(); ?>">Read bio →</a></div>
                    </article>
                <?php endwhile; ?>
            </div>
        <?php else : ?>
            <div class="sb-faculty-grid-block__empty"><p>No Faculty profiles have been published yet.</p></div>
        <?php endif; wp_reset_postdata(); ?>
    </div>
<?php if (!$is_editor_preview) : ?></section><?php endif; ?>
