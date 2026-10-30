<?php
/**
 * Layout: Übernachten (layout_uebernachten) – head next to accommodation cards (type label + icon, name, phone, website) from Geteilte Inhalte → Übernachten, off-white or white; Programm & Event, Anreise, Ride-Out.
 *
 * @package loveride
 */

$lr_data = (array) lr_get_option( 'geteilt_uebernachten', array() );

$lr_head = array(
	'tagline' => (string) ( $lr_data['tagline'] ?? '' ),
	'titel'   => (string) ( $lr_data['titel'] ?? '' ),
	'text'    => (string) ( $lr_data['text'] ?? '' ),
);

$lr_unterkuenfte = array_values( array_filter(
	(array) ( ( $lr_data['unterkuenfte'] ?? null ) ?: array() ),
	fn( $unterkunft ) => trim( (string) ( $unterkunft['name'] ?? '' ) ) !== ''
) );

$lr_typen = array(
	'hotel'   => __( 'Hotels', 'loveride' ),
	'camping' => __( 'Camping', 'loveride' ),
);

if ( ! array_filter( $lr_head ) && ! $lr_unterkuenfte ) {
	return;
}

$lr_scheme = get_sub_field( 'hintergrund' ) === 'weiss' ? 'weiss' : 'offwhite';
?>
<section <?= lr_section_attrs( array( 'scheme-' . $lr_scheme ), array( 'id' => 'uebernachten' ) ); ?>>
	<div class="container">
		<div class="uebernachten">

			<?php get_template_part( 'template_parts/components/section_head', null, $lr_head + array( 'animate' => true ) ); ?>

			<?php if ( $lr_unterkuenfte ) : ?>
				<ul class="unterkuenfte" data-gsap-cards>
					<?php foreach ( $lr_unterkuenfte as $lr_unterkunft ) : ?>
						<?php
						$lr_typ     = ( $lr_unterkunft['typ'] ?? '' ) === 'camping' ? 'camping' : 'hotel';
						$lr_telefon = trim( (string) ( $lr_unterkunft['telefon'] ?? '' ) );
						$lr_website = (array) ( ( $lr_unterkunft['website'] ?? null ) ?: array() );
						?>
						<li class="unterkunft">
							<?= lr_icon( $lr_typ, 'unterkunft__icon' ); ?>

							<div class="unterkunft__content">
								<p class="unterkunft__typ"><?= esc_html( $lr_typen[ $lr_typ ] ); ?></p>
								<h3 class="unterkunft__title"><?= esc_html( $lr_unterkunft['name'] ); ?></h3>
							</div>

							<?php if ( $lr_telefon !== '' ) : ?>
								<a class="unterkunft__telefon" href="tel:<?= esc_attr( preg_replace( '/[^\d+]/', '', $lr_telefon ) ); ?>"><?= lr_icon( 'call', 'unterkunft__telefon-icon' ); ?><?= esc_html( $lr_telefon ); ?></a>
							<?php endif; ?>

							<?php if ( ! empty( $lr_website['url'] ) ) : ?>
								<a class="unterkunft__cta btn--link" <?= lr_link_attrs( $lr_website ); ?>><?= esc_html( $lr_website['title'] ?: __( 'Zur Website', 'loveride' ) ); ?><?= lr_ui_icon( 'chevron_right' ); ?></a>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

		</div>
	</div>
</section>
