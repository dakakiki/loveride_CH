<?php
/**
 * Layout: Bild-Karten (layout_bildkarten) – section head and dark image cards (photo, title, text, optional link → whole card clickable) in 4 / 2 / 1 columns; Event „Attraktionen“.
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
?>
<section <?= lr_section_attrs(); ?>>
	<div class="container">

		<?php get_template_part( 'template_parts/components/section_head', null, $lr_head + array( 'animate' => true ) ); ?>

		<?php if ( $lr_karten ) : ?>
			<ul class="bild-karten" data-gsap-cards>
				<?php foreach ( $lr_karten as $lr_karte ) : ?>
					<?php $lr_link = (array) ( $lr_karte['link'] ?: array() ); ?>
					<li>
						<article class="bild-karte<?= ! empty( $lr_link['url'] ) ? ' bild-karte--link' : ''; ?>">
							<div class="bild-karte__media">
								<?= lr_acf_image( $lr_karte['bild'], 'medium_large', array(
									'class'   => 'bild-karte__image',
									'sizes'   => '(min-width: 991px) 308px, (min-width: 640px) 50vw, 100vw',
									'loading' => 'lazy',
								) ); ?>
							</div>
							<div class="bild-karte__body">
								<h3 class="bild-karte__title"><?= esc_html( $lr_karte['titel'] ); ?></h3>
								<?php if ( trim( (string) $lr_karte['text'] ) !== '' ) : ?>
									<p class="bild-karte__text"><?= esc_html( $lr_karte['text'] ); ?></p>
								<?php endif; ?>
								<?php if ( ! empty( $lr_link['url'] ) ) : ?>
									<a class="bild-karte__cta btn--link" <?= lr_link_attrs( $lr_link ); ?>><?= esc_html( $lr_link['title'] ?: __( 'Mehr erfahren', 'loveride' ) ); ?><?= lr_ui_icon( 'chevron_right' ); ?></a>
								<?php endif; ?>
							</div>
						</article>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

	</div>
</section>
