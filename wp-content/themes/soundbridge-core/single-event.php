<?php
get_header();

while ( have_posts() ) :
	the_post();

	$post_id = get_the_ID();
	$event_details = array(
		'event_date' => 'Date',
		'event_time' => 'Time',
		'location'   => 'Location',
		'cost'       => 'Cost',
		'audience'   => 'Audience',
	);
	?>
	<section class="sb-detail-hero">
		<div class="sb-container">
			<p class="sb-eyebrow">
				<?php echo esc_html( get_post_meta( $post_id, 'sb_event_date', true ) ); ?>
			</p>
			<h1><?php the_title(); ?></h1>
		</div>
	</section>

	<div class="sb-container sb-detail-layout">
		<article class="sb-prose">
			<h2>About This Event</h2>
			<?php the_content(); ?>

			<h2>Event Details</h2>
			<div class="sb-info-grid">
				<?php foreach ( $event_details as $key => $label ) : ?>
					<?php $value = get_post_meta( $post_id, 'sb_' . $key, true ); ?>
					<?php if ( $value ) : ?>
						<div>
							<span><?php echo esc_html( $label ); ?></span>
							<strong><?php echo esc_html( $value ); ?></strong>
						</div>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
		</article>
	</div>
	<?php
endwhile;

get_footer();
