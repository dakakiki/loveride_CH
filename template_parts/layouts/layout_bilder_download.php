<?php
/**
 * Layout: Bilder-Download (layout_bilder_download) – section head with buttons on the right (ZIP, Logos) and a gallery of press photos as cards (photo, title, download of the original file) in 3 / 2 / 1 columns; Medien #bilder.
 *
 * @package loveride
 */

$lr_bilder = array_values( array_filter( (array) ( get_sub_field( 'bilder' ) ?: array() ), fn( $bild ) => ! empty( $bild['ID'] ) ) );

if ( ! $lr_bilder ) {
	return;
}

$lr_hintergrund = get_sub_field( 'hintergrund' ) === 'weiss' ? 'weiss' : 'offwhite';
$lr_head        = array(
	'tagline' => (string) get_sub_field( 'tagline' ),
	'titel'   => (string) get_sub_field( 'titel' ),
	'text'    => (string) get_sub_field( 'text' ),
);
?>
<section <?= lr_section_attrs( array( 'scheme-' . $lr_hintergrund ), array( 'id' => 'bilder' ) ); ?>>
	<div class="container">

		<div class="bilder-download__kopf">
			<?php get_template_part( 'template_parts/components/section_head', null, $lr_head + array( 'animate' => true ) ); ?>
			<?php get_template_part( 'template_parts/components/buttons', null, array( 'buttons' => get_sub_field( 'buttons' ), 'class' => 'bilder-download__buttons fade-up' ) ); ?>
		</div>

		<ul class="bilder-download" data-gsap-cards>
			<?php foreach ( $lr_bilder as $lr_bild ) : ?>
				<?php $lr_titel = trim( (string) ( $lr_bild['title'] ?? '' ) ) ?: (string) ( $lr_bild['filename'] ?? '' ); ?>
				<li class="bild-download">
					<div class="bild-download__foto">
						<?= lr_acf_image( $lr_bild, 'medium_large', array(
							'class'   => 'bild-download__bild',
							'sizes'   => '(min-width: 991px) 33vw, (min-width: 640px) 50vw, 100vw',
							'loading' => 'lazy',
							'alt'     => $lr_bild['alt'] ?: $lr_titel,
						) ); ?>
					</div>
					<a class="bild-download__info" href="<?= esc_url( wp_get_original_image_url( $lr_bild['ID'] ) ?: $lr_bild['url'] ); ?>" download>
						<span class="bild-download__titel"><?= esc_html( $lr_titel ); ?></span>
						<?= lr_icon( 'download', 'bild-download__icon' ); ?>
						<span class="screen-reader-text"><?php esc_html_e( 'Bild herunterladen', 'loveride' ); ?></span>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>

	</div>
</section>
