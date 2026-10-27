<?php
/**
 * Icons: Material Symbols from assets/icons, inline output and choices for ACF "icon" select fields.
 *
 * @package loveride
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function lr_icon_labels(): array {

	return array(
		'account_balance'     => 'Bank / Überweisung',
		'accessible'          => 'Barrierefrei',
		'accessible_forward'  => 'Mobilität',
		'airport_shuttle'     => 'Shuttle',
		'badge'               => 'Badge',
		'call'                => 'Telefon',
		'camping'             => 'Camping',
		'celebration'         => 'Fest',
		'checkroom'           => 'Kleidung',
		'confirmation_number' => 'Ticket',
		'construction'        => 'Aufbau / Abbau',
		'description'         => 'Dokument',
		'diversity_1'         => 'Gemeinschaft',
		'diversity_3'         => 'Gönner',
		'do_not_touch'        => 'Nicht abklatschen',
		'download'            => 'Download',
		'event'               => 'Kalender',
		'favorite'            => 'Herz',
		'groups'              => 'Gruppe',
		'handshake'           => 'Partnerschaft',
		'health_and_safety'   => 'Sicherheit / Risiko',
		'history'             => 'Geschichte',
		'hotel'               => 'Hotel',
		'icecream'            => 'Glace',
		'info'                => 'Info',
		'local_parking'       => 'Parkplatz',
		'local_police'        => 'Polizei',
		'location_city'       => 'Gemeinde',
		'location_on'         => 'Standort',
		'login'               => 'Login',
		'lunch_dining'        => 'Essen',
		'mail'                => 'E-Mail',
		'military_tech'       => 'Medaille',
		'no_drinks'           => 'Kein Alkohol',
		'open_in_new'         => 'Externer Link',
		'pedal_bike'          => 'Velo',
		'pets'                => 'Hunde',
		'photo_camera'        => 'Foto & Film',
		'play_circle'         => 'Video',
		'rainy'               => 'Wetter',
		'route'               => 'Route / Zufahrt',
		'savings'             => 'Spendensäule',
		'schedule'            => 'Zeit',
		'social_distance'     => 'Abstand',
		'speed'               => 'Tempo',
		'sports_motorsports'  => 'Helm',
		'support_agent'       => 'Betreuung',
		'traffic'             => 'Verkehr / Sperrung',
		'two_wheeler'         => 'Töff',
		'volunteer_activism'  => 'Spenden',
		'wc'                  => 'Toiletten',
		'wheelchair_pickup'   => 'Rollstuhl',
		'workspace_premium'   => 'Auszeichnung',
	);
}

function lr_icon_choices(): array {

	static $choices = null;

	if ( $choices !== null ) {
		return $choices;
	}

	$labels  = lr_icon_labels();
	$choices = array();

	foreach ( glob( LR_THEME_PATH . '/assets/icons/*.svg' ) ?: array() as $file ) {
		$name             = basename( $file, '.svg' );
		$choices[ $name ] = $labels[ $name ] ?? $name;
	}

	asort( $choices, SORT_NATURAL | SORT_FLAG_CASE );

	return $choices;
}

function lr_icon( ?string $name, string $class = '' ): string {

	$name = sanitize_key( (string) $name );

	if ( $name === '' ) {
		return '';
	}

	$file = LR_THEME_PATH . '/assets/icons/' . $name . '.svg';

	if ( ! file_exists( $file ) ) {
		return '';
	}

	$svg = file_get_contents( $file );

	return (string) preg_replace(
		'/<svg\b/',
		'<svg class="icon ' . esc_attr( trim( 'icon--' . $name . ' ' . $class ) ) . '" aria-hidden="true" focusable="false"',
		$svg,
		1
	);
}

add_filter( 'acf/prepare_field/name=icon', function ( $field ) {

	if ( $field['type'] === 'select' ) {
		$field['choices'] = lr_icon_choices();
	}

	return $field;
} );

add_action( 'acf/input/admin_enqueue_scripts', function () {

	$rel = lr_pick_dist( 'js/admin-acf.min.js', 'js/admin-acf.js' );

	wp_enqueue_script( 'lr-admin-acf', lr_dist_uri( $rel ), array( 'acf-input' ), lr_dist_ver( $rel ), true );

	wp_localize_script( 'lr-admin-acf', 'lrAdmin', array(
		'iconUrl' => LR_THEME_URI . '/assets/icons/',
	) );
} );
