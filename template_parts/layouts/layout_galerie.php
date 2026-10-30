<?php
/**
 * Layout: Galerie-Slider (layout_galerie) – section head, aftermovie poster and a photo slider bleeding to the right edge with dots and arrows; photos and the aftermovie (YouTube without cookies) open in a lightbox; Home „So fühlt sich der Love Ride an“.
 *
 * @package loveride
 */

$lr_head = array(
	'tagline' => (string) get_sub_field( 'tagline' ),
	'titel'   => (string) get_sub_field( 'titel' ),
	'text'    => (string) get_sub_field( 'text' ),
);

$lr_bilder  = array_values( array_filter( (array) ( get_sub_field( 'bilder' ) ?: array() ) ) );
$lr_poster  = get_sub_field( 'vorschaubild' );
$lr_youtube = lr_youtube_id( (string) get_sub_field( 'video' ) );

if ( ! array_filter( $lr_head ) && ! $lr_bilder && ! $lr_youtube && ! $lr_poster ) {
	return;
}

$lr_poster_html = $lr_poster
	? lr_acf_image( $lr_poster, 'full', array(
		'class'   => 'galerie__poster',
		'alt'     => '',
		'sizes'   => '(min-width: 1440px) 1280px, 100vw',
		'loading' => 'lazy',
	) )
	: ( $lr_youtube ? '<img class="galerie__poster" src="' . esc_url( 'https://i.ytimg.com/vi/' . $lr_youtube . '/hqdefault.jpg' ) . '" alt="" loading="lazy" referrerpolicy="no-referrer">' : '' );
?>
<section <?= lr_section_attrs(); ?>>
	<div class="container">

		<?php get_template_part( 'template_parts/components/section_head', null, $lr_head + array( 'animate' => true ) ); ?>

		<?php if ( $lr_poster_html ) : ?>
			<div class="galerie__video fade-up">
				<?php if ( $lr_youtube ) : ?>
					<button type="button" class="galerie__play" data-youtube="<?= esc_attr( $lr_youtube ); ?>" data-title="<?= esc_attr( wp_strip_all_tags( $lr_head['titel'] ) ?: __( 'Aftermovie', 'loveride' ) ); ?>" aria-label="<?= esc_attr__( 'Aftermovie abspielen (YouTube)', 'loveride' ); ?>">
						<?= $lr_poster_html; ?>
						<span class="galerie__play-icon"><?= lr_icon( 'play_circle' ); ?></span>
					</button>
				<?php else : ?>
					<?= $lr_poster_html; ?>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( $lr_bilder ) : ?>
			<div class="galerie__slider" data-galerie>
				<ul class="galerie__track" aria-label="<?= esc_attr__( 'Fotogalerie', 'loveride' ); ?>" data-gsap-cards>
					<?php foreach ( $lr_bilder as $lr_i => $lr_bild ) : ?>
						<?php /* translators: 1: image number, 2: image count */ ?>
						<?php $lr_label = sprintf( __( 'Bild %1$d von %2$d vergrössern', 'loveride' ), $lr_i + 1, count( $lr_bilder ) ) . ( ! empty( $lr_bild['alt'] ) ? ': ' . $lr_bild['alt'] : '' ); ?>
						<li class="galerie__item">
							<a class="galerie__link" href="<?= esc_url( wp_get_attachment_image_url( $lr_bild['ID'], 'full' ) ); ?>" data-lightbox-index="<?= (int) $lr_i; ?>" aria-label="<?= esc_attr( $lr_label ); ?>">
								<?= lr_acf_image( $lr_bild, 'medium_large', array(
									'class'   => 'galerie__image',
									'sizes'   => '(min-width: 991px) 405px, (min-width: 640px) 50vw, 300px',
									'loading' => 'lazy',
								) ); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>

				<?php if ( count( $lr_bilder ) > 1 ) : ?>
					<div class="galerie__controls">
						<div class="galerie__dots" data-galerie-dots></div>
						<div class="galerie__arrows">
							<button type="button" class="galerie__arrow" aria-label="<?= esc_attr__( 'Vorherige Bilder', 'loveride' ); ?>" data-galerie-prev><?= lr_ui_icon( 'arrow_back' ); ?></button>
							<button type="button" class="galerie__arrow" aria-label="<?= esc_attr__( 'Nächste Bilder', 'loveride' ); ?>" data-galerie-next><?= lr_ui_icon( 'arrow_forward' ); ?></button>
						</div>
					</div>
				<?php endif; ?>
			</div>
		<?php endif; ?>

	</div>

	<?php if ( $lr_bilder || $lr_youtube ) : ?>
		<?php get_template_part( 'template_parts/components/lightbox' ); ?>
	<?php endif; ?>
</section>
