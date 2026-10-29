<?php
/**
 * Layout: Dein Love Ride (layout_dein_love_ride) – dark target-group cards (image, title, text, link) from Geteilte Inhalte → Dein Love Ride, whole card clickable; Home, can be placed on any page.
 *
 * @package loveride
 */

$lr_data = (array) lr_get_option( 'geteilt_dein_love_ride', array() );

$lr_head = array(
	'tagline' => (string) ( $lr_data['tagline'] ?? '' ),
	'titel'   => (string) ( $lr_data['titel'] ?? '' ),
	'text'    => (string) ( $lr_data['text'] ?? '' ),
);

$lr_karten = array_values( array_filter(
	(array) ( ( $lr_data['karten'] ?? null ) ?: array() ),
	fn( $karte ) => trim( (string) ( $karte['titel'] ?? '' ) ) !== ''
) );

if ( ! array_filter( $lr_head ) && ! $lr_karten ) {
	return;
}
?>
<section <?= lr_section_attrs(); ?>>
	<div class="container">

		<?php get_template_part( 'template_parts/components/section_head', null, $lr_head + array( 'animate' => true ) ); ?>

		<?php if ( $lr_karten ) : ?>
			<ul class="love-ride-karten" data-gsap-cards>
				<?php foreach ( $lr_karten as $lr_karte ) : ?>
					<?php $lr_link = (array) ( $lr_karte['link'] ?: array() ); ?>
					<li class="love-ride-karte<?= ! empty( $lr_link['url'] ) ? ' love-ride-karte--link' : ''; ?>">

						<div class="love-ride-karte__media">
							<?php if ( ! empty( $lr_karte['bild'] ) ) : ?>
								<?= lr_acf_image( $lr_karte['bild'], 'medium_large', array(
									'class'   => 'love-ride-karte__image',
									'sizes'   => '(min-width: 991px) 33vw, (min-width: 640px) 50vw, 100vw',
									'loading' => 'lazy',
								) ); ?>
							<?php endif; ?>
						</div>

						<div class="love-ride-karte__body">
							<div class="love-ride-karte__content">
								<h3 class="love-ride-karte__title"><?= esc_html( $lr_karte['titel'] ); ?></h3>

								<?php if ( trim( (string) $lr_karte['text'] ) !== '' ) : ?>
									<p class="love-ride-karte__text"><?= esc_html( $lr_karte['text'] ); ?></p>
								<?php endif; ?>
							</div>

							<?php if ( ! empty( $lr_link['url'] ) ) : ?>
								<a class="love-ride-karte__cta btn--link" <?= lr_link_attrs( $lr_link ); ?>><?= esc_html( $lr_link['title'] ?: __( 'Mehr erfahren', 'loveride' ) ); ?><?= lr_ui_icon( 'chevron_right' ); ?></a>
							<?php endif; ?>
						</div>

					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

	</div>
</section>
