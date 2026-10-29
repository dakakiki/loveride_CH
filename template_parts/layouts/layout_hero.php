<?php
/**
 * Layout: Hero-Slider (layout_hero) – full-screen slides with image/video background, used on Home.
 *
 * @package loveride
 */

$lr_slides = array_values( array_filter( (array) ( get_sub_field( 'slides' ) ?: array() ), function ( $slide ) {
	return ! empty( $slide['hintergrund']['bild'] ) || ! empty( $slide['inhalt']['titel'] );
} ) );

if ( ! $lr_slides ) {
	return;
}

$lr_count    = count( $lr_slides );
$lr_autoplay = $lr_count > 1 && get_sub_field( 'autoplay' );
$lr_interval = max( 3, (int) ( get_sub_field( 'intervall' ) ?: 7 ) );
?>
<section <?= lr_section_attrs( array(), array(
	'data-hero'             => true,
	'data-autoplay'         => $lr_autoplay ? 'true' : 'false',
	'data-interval'         => $lr_interval,
	'aria-roledescription'  => $lr_count > 1 ? __( 'Karussell', 'loveride' ) : null,
	'aria-label'            => __( 'Slideshow', 'loveride' ),
) ); ?>>

	<div class="hero-slides" aria-live="<?= $lr_autoplay ? 'off' : 'polite'; ?>" data-hero-slides>

		<?php foreach ( $lr_slides as $lr_i => $lr_slide ) : ?>
			<?php
			$lr_inhalt     = $lr_slide['inhalt'] ?? array();
			$lr_hg         = $lr_slide['hintergrund'] ?? array();
			$lr_is_first   = $lr_i === 0;
			$lr_title_tag  = $lr_is_first ? 'h1' : 'h2';
			$lr_slide_attr = $lr_is_first ? '' : ' aria-hidden="true" inert';
			?>
			<div class="hero-slide<?= $lr_is_first ? ' is-active' : ''; ?>" role="group" aria-roledescription="<?= esc_attr__( 'Slide', 'loveride' ); ?>" aria-label="<?= esc_attr( sprintf( __( '%1$d von %2$d', 'loveride' ), $lr_i + 1, $lr_count ) ); ?>" data-hero-slide<?= $lr_slide_attr; ?>>

				<?php
				get_template_part( 'template_parts/components/hero_media', null, array(
					'bild'  => $lr_hg['bild'] ?? null,
					'video' => ( $lr_hg['typ'] ?? '' ) === 'video' ? ( $lr_hg['video'] ?? null ) : null,
					'eager' => $lr_is_first,
				) );
				?>

				<div class="hero__inner">
					<div class="hero__content">

						<?php if ( ! empty( $lr_inhalt['tagline'] ) ) : ?>
							<p class="hero__tagline tagline" data-hero-item><?= esc_html( $lr_inhalt['tagline'] ); ?></p>
						<?php endif; ?>

						<?php if ( ! empty( $lr_inhalt['titel'] ) ) : ?>
							<<?= $lr_title_tag; ?> class="hero__title" data-hero-item><?= wp_kses_post( $lr_inhalt['titel'] ); ?></<?= $lr_title_tag; ?>>
						<?php endif; ?>

						<?php if ( ! empty( $lr_inhalt['text'] ) ) : ?>
							<div class="hero__text" data-hero-item><?= wp_kses_post( $lr_inhalt['text'] ); ?></div>
						<?php endif; ?>

					</div>
				</div>

				<?php if ( ! empty( $lr_inhalt['logo'] ) ) : ?>
					<?= lr_acf_image( $lr_inhalt['logo'], 'medium', array(
						'class'          => 'hero__logo',
						'data-hero-item' => '',
						'loading'        => $lr_is_first ? 'eager' : 'lazy',
					) ); ?>
				<?php endif; ?>

			</div>
		<?php endforeach; ?>

	</div>

	<?php get_template_part( 'template_parts/components/hero_controls', null, array( 'count' => $lr_count ) ); ?>

</section>
