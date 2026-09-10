<?php
get_header();

while ( have_posts() ) :
	the_post();

	$post_id = get_the_ID();
	$registration_url = get_post_meta( $post_id, 'sb_registration_url', true ) ?: home_url( '/contact' );
	$program_details = array(
		'age'            => 'Ages',
		'level'          => 'Level',
		'instrument'     => 'Instrument',
		'schedule'       => 'Schedule',
		'session_length' => 'Session length',
		'location'       => 'Location',
		'cost'           => 'Tuition',
	);
	?>
	<section
		class="sb-detail-hero"
		<?php if ( has_post_thumbnail() ) : ?>
			style="--hero: url('<?php echo esc_url( get_the_post_thumbnail_url( $post_id, 'full' ) ); ?>')"
		<?php endif; ?>
	>
		<div class="sb-container">
			<p class="sb-eyebrow">
				<?php echo esc_html( get_post_meta( $post_id, 'sb_status_label', true ) ); ?>
			</p>
			<h1><?php the_title(); ?></h1>
			<p class="sb-lead">
				<?php echo esc_html( get_post_meta( $post_id, 'sb_tagline', true ) ); ?>
			</p>
		</div>
	</section>

	<div class="sb-container sb-detail-layout">
		<article class="sb-prose">
			<h2>About the Program</h2>
			<?php the_content(); ?>

			<h2>Program Details</h2>
			<div class="sb-info-grid">
				<?php foreach ( $program_details as $key => $label ) : ?>
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

		<aside class="sb-sidebar">
			<h3>Interested in this program?</h3>
			<p>Contact SoundBridge for current enrollment, tuition, and scholarship information.</p>
			<a class="sb-btn" href="<?php echo esc_url( $registration_url ); ?>">
				Register / Ask a Question
			</a>
		</aside>
	</div>
	<?php
endwhile;

get_footer();
