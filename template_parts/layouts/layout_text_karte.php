<?php
/**
 * Layout: Text & Karte (layout_text_karte) – text column (head with red-square list, buttons) next to a map with one layer from Geteilte Inhalte → Anreise & Parken or an own route file (GPX / GeoJSON), or a placeholder box while there is nothing to show; normal or wide map; Anreise „Rücksicht auf der Strecke“, Ride-Out „Route“.
 *
 * @package loveride
 */

$lr_head = array(
	'tagline' => (string) get_sub_field( 'tagline' ),
	'titel'   => (string) get_sub_field( 'titel' ),
	'text'    => (string) get_sub_field( 'text' ),
);

$lr_karte = lr_anreise_karte( (array) ( lr_get_option( 'geteilt_anreise', array() )['karte'] ?? array() ) );
$lr_ebene = null;

if ( get_sub_field( 'quelle' ) === 'datei' ) {
	$lr_ebene = array(
		'name'    => (string) get_sub_field( 'platzhalter_titel' ),
		'icon'    => 'route',
		'marker'  => array(),
		'geojson' => lr_anreise_geojson( get_sub_field( 'datei' ) ),
	);
} else {
	foreach ( $lr_karte['ebenen'] as $lr_item ) {
		if ( (string) get_sub_field( 'ebene' ) !== '' && sanitize_title( $lr_item['name'] ) === get_sub_field( 'ebene' ) ) {
			$lr_ebene = $lr_item;
			break;
		}
	}
}

$lr_has_map = $lr_ebene && ( $lr_ebene['marker'] || $lr_ebene['geojson'] );

if ( ! array_filter( $lr_head ) && ! $lr_ebene ) {
	return;
}

$lr_scheme = get_sub_field( 'hintergrund' ) === 'weiss' ? 'weiss' : 'offwhite';
$lr_breit  = get_sub_field( 'kartenbreite' ) === 'breit';
$lr_icon   = (string) get_sub_field( 'icon' ) ?: ( $lr_ebene['icon'] ?? '' ) ?: 'traffic';
?>
<section <?= lr_section_attrs( array_filter( array( 'scheme-' . $lr_scheme, $lr_breit ? 'text-karte--breit' : '' ) ), array( 'data-anreise' => $lr_has_map ) ); ?>>
	<div class="container">
		<div class="text-karte">

			<div class="text-karte__text">
				<?php get_template_part( 'template_parts/components/section_head', null, $lr_head + array( 'animate' => true ) ); ?>
				<?php get_template_part( 'template_parts/components/buttons', null, array( 'buttons' => get_sub_field( 'buttons' ), 'class' => 'fade-up' ) ); ?>
			</div>

			<?php if ( $lr_has_map ) : ?>
				<div class="text-karte__map fade-up" data-map="<?= esc_attr( wp_json_encode( array(
					'ziel'   => array(
						'titel' => $lr_karte['ziel'],
						'pos'   => $lr_karte['pos'],
						'icon'  => lr_icon( 'location_on' ),
					),
					'zoom'   => $lr_karte['zoom'],
					'ebenen' => array( array(
						'icon'    => lr_icon( $lr_ebene['icon'] ),
						'aktiv'   => true,
						'marker'  => $lr_ebene['marker'],
						'geojson' => $lr_ebene['geojson'],
					) ),
				) ) ); ?>" role="region" aria-label="<?= esc_attr( $lr_ebene['name'] ?: __( 'Karte', 'loveride' ) ); ?>"></div>
			<?php elseif ( $lr_ebene ) : ?>
				<div class="text-karte__platzhalter fade-up">
					<?= lr_icon( $lr_icon, 'text-karte__platzhalter-icon' ); ?>
					<p class="text-karte__platzhalter-titel"><?= esc_html( (string) get_sub_field( 'platzhalter_titel' ) ?: $lr_ebene['name'] ); ?></p>
					<?php if ( trim( (string) get_sub_field( 'platzhalter_text' ) ) !== '' ) : ?>
						<p class="text-karte__platzhalter-text"><?= esc_html( get_sub_field( 'platzhalter_text' ) ); ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>

		</div>
	</div>
</section>
