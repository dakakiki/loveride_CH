<?php
/**
 * Layout: Karten-Grid (layout_karten) – section head and cards (icon on top or left, label, title, text, optional link; single cards red) in 2 / 3 / 4 columns on white, off-white or dark, optional red top line; Anreise „Notfall & Kontakte“, other card sections.
 *
 * @package loveride
 */

$lr_head = array(
	'tagline' => (string) get_sub_field( 'tagline' ),
	'titel'   => (string) get_sub_field( 'titel' ),
	'text'    => (string) get_sub_field( 'text' ),
);

$lr_karten = array_values( array_filter(
	(array) ( get_sub_field( 'karten' ) ?: array() ),
	fn( $karte ) => trim( (string) ( $karte['titel'] ?? '' ) ) !== ''
) );

if ( ! $lr_karten && ! array_filter( $lr_head ) ) {
	return;
}

$lr_hintergrund = in_array( get_sub_field( 'hintergrund' ), array( 'offwhite', 'dunkel' ), true ) ? get_sub_field( 'hintergrund' ) : 'weiss';
$lr_spalten     = in_array( (string) get_sub_field( 'spalten' ), array( '2', '3', '4' ), true ) ? (string) get_sub_field( 'spalten' ) : '3';
$lr_titel_size  = in_array( get_sub_field( 'titelgroesse' ), array( 'klein', 'gross' ), true ) ? get_sub_field( 'titelgroesse' ) : 'standard';

$lr_classes = array(
	'scheme-' . $lr_hintergrund,
	'karten--' . $lr_hintergrund,
	'karten--spalten-' . $lr_spalten,
	'karten--titel-' . $lr_titel_size,
	get_sub_field( 'icon_position' ) === 'links' ? 'karten--icon-links' : '',
	get_sub_field( 'linie' ) ? 'karten--linie' : '',
);
?>
<section <?= lr_section_attrs( array_filter( $lr_classes ) ); ?>>
	<div class="container">

		<?php get_template_part( 'template_parts/components/section_head', null, $lr_head + array( 'animate' => true ) ); ?>

		<?php if ( $lr_karten ) : ?>
			<ul class="karten" data-gsap-cards>
				<?php foreach ( $lr_karten as $lr_karte ) : ?>
					<?php $lr_link = (array) ( $lr_karte['link'] ?: array() ); ?>
					<li class="karte<?= ! empty( $lr_karte['rot'] ) ? ' karte--rot' : ''; ?>">
						<?= lr_icon( $lr_karte['icon'] ?? '', 'karte__icon' ); ?>

						<div class="karte__body">
							<?php if ( trim( (string) $lr_karte['oberzeile'] ) !== '' ) : ?>
								<p class="karte__oberzeile"><?= esc_html( $lr_karte['oberzeile'] ); ?></p>
							<?php endif; ?>

							<h3 class="karte__titel"><?= esc_html( $lr_karte['titel'] ); ?></h3>

							<?php if ( trim( (string) $lr_karte['text'] ) !== '' ) : ?>
								<p class="karte__text"><?= nl2br( esc_html( $lr_karte['text'] ) ); ?></p>
							<?php endif; ?>

							<?php if ( ! empty( $lr_link['url'] ) ) : ?>
								<a class="karte__link btn--link" <?= lr_link_attrs( $lr_link ); ?>><?= esc_html( $lr_link['title'] ?: __( 'Mehr erfahren', 'loveride' ) ); ?><?= lr_ui_icon( 'chevron_right' ); ?></a>
							<?php endif; ?>
						</div>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

	</div>
</section>
