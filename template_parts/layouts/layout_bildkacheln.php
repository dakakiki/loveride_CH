<?php
/**
 * Layout: Bild-Kacheln (layout_bildkacheln) – section head in two columns and full-width image tiles (numbered, dark / red / light by order), each linking to a page; Home „Drei Welten“.
 *
 * @package loveride
 */

$lr_head = array(
	'tagline' => (string) get_sub_field( 'tagline' ),
	'titel'   => (string) get_sub_field( 'titel' ),
	'text'    => (string) get_sub_field( 'text' ),
);

$lr_kacheln = array_values( array_filter(
	(array) ( get_sub_field( 'kacheln' ) ?: array() ),
	fn( $kachel ) => ! empty( $kachel['bild'] ) || trim( (string) ( $kachel['titel'] ?? '' ) ) !== ''
) );

if ( ! array_filter( $lr_head ) && ! $lr_kacheln ) {
	return;
}

$lr_farben = array( 'dunkel', 'rot', 'hell' );
?>
<section <?= lr_section_attrs(); ?>>

	<?php if ( array_filter( $lr_head ) ) : ?>
		<div class="container">
			<?php get_template_part( 'template_parts/components/section_head', null, $lr_head + array( 'split' => true, 'animate' => true ) ); ?>
		</div>
	<?php endif; ?>

	<?php if ( $lr_kacheln ) : ?>
		<ul class="bild-kacheln" data-gsap-cards style="--kacheln: <?= (int) count( $lr_kacheln ); ?>">
			<?php foreach ( $lr_kacheln as $lr_i => $lr_kachel ) : ?>
				<?php $lr_link = (array) ( $lr_kachel['link'] ?: array() ); ?>
				<li class="bild-kachel bild-kachel--<?= esc_attr( $lr_farben[ $lr_i % 3 ] ); ?><?= ! empty( $lr_link['url'] ) ? ' bild-kachel--link' : ''; ?>">

					<div class="bild-kachel__media">
						<?= lr_acf_image( $lr_kachel['bild'], 'large', array(
							'class'   => 'bild-kachel__image',
							'sizes'   => '(min-width: 991px) ' . (int) round( 100 / count( $lr_kacheln ) ) . 'vw, 100vw',
							'loading' => 'lazy',
						) ); ?>
					</div>

					<div class="bild-kachel__body">
						<p class="bild-kachel__number" aria-hidden="true"><?= esc_html( sprintf( '%02d', $lr_i + 1 ) ); ?></p>

						<?php if ( $lr_kachel['titel'] ) : ?>
							<h3 class="bild-kachel__title"><?= esc_html( $lr_kachel['titel'] ); ?></h3>
						<?php endif; ?>

						<?php if ( $lr_kachel['text'] ) : ?>
							<p class="bild-kachel__text"><?= esc_html( $lr_kachel['text'] ); ?></p>
						<?php endif; ?>

						<?php if ( ! empty( $lr_link['url'] ) ) : ?>
							<a class="bild-kachel__cta btn--link" <?= lr_link_attrs( $lr_link ); ?>><?= esc_html( $lr_link['title'] ?: __( 'Mehr erfahren', 'loveride' ) ); ?><?= lr_ui_icon( 'chevron_right' ); ?></a>
						<?php endif; ?>
					</div>

				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>

</section>
