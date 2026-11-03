<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const LR_POPUPS = array(
	'loverider'  => 'loverider-anmeldung',
	'aussteller' => 'aussteller-anmeldung',
);

function lr_popup_use( ?string $popup = null ): array {

	static $used = array();

	if ( $popup !== null && isset( LR_POPUPS[ $popup ] ) ) {
		$used[ $popup ] = true;
	}

	return array_keys( $used );
}

function lr_popup_daten( string $popup ): array {

	$popups = (array) lr_get_option( 'geteilt_popups', array() );

	return (array) ( $popups[ $popup ] ?? array() );
}

function lr_popup_attrs( string $popup, string $wahl = '' ): string {

	if ( ! isset( LR_POPUPS[ $popup ] ) || ! (int) ( lr_popup_daten( $popup )['formular'] ?? 0 ) ) {
		return '';
	}

	lr_popup_use( $popup );

	return sprintf(
		'href="#%1$s" data-popup="%1$s" aria-haspopup="dialog"%2$s',
		esc_attr( LR_POPUPS[ $popup ] ),
		$wahl !== '' ? ' data-popup-wahl="' . esc_attr( $wahl ) . '"' : ''
	);
}

add_action( 'wp_footer', function () {

	$popups = lr_popup_use();

	if ( is_admin() || ! $popups ) {
		return;
	}

	foreach ( $popups as $popup ) {
		get_template_part( 'template_parts/global/popup', null, lr_popup_daten( $popup ) + array( 'id' => LR_POPUPS[ $popup ] ) );
	}

	$rel = lr_pick_dist( 'js/popups.min.js', 'js/popups.js' );

	wp_enqueue_script( 'lr-popups', lr_dist_uri( $rel ), array(), lr_dist_ver( $rel ), array(
		'in_footer' => true,
		'strategy'  => 'defer',
	) );
}, 5 );
