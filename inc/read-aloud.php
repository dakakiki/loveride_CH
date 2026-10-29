<?php
/**
 * Read aloud: option check and script for the "Seite vorlesen" floater (Theme-Einstellungen → Vorlesen), all frontend pages.
 *
 * @package loveride
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function lr_read_aloud_enabled(): bool {

	return (bool) lr_get_option( 'vorlesen_aktiv', false );
}

add_action( 'wp_enqueue_scripts', function () {

	if ( is_admin() || ! lr_read_aloud_enabled() ) {
		return;
	}

	$rel = lr_pick_dist( 'js/read-aloud.min.js', 'js/read-aloud.js' );

	wp_enqueue_script( 'lr-read-aloud', lr_dist_uri( $rel ), array(), lr_dist_ver( $rel ), array(
		'in_footer' => true,
		'strategy'  => 'defer',
	) );

}, 20 );
