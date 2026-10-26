<?php
/**
 * Fonts: preload of the self-hosted Open Sans variable font (latin subset).
 *
 * @package loveride
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_head', function () {

	printf(
		'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
		esc_url( LR_THEME_URI . '/assets/fonts/OpenSans/OpenSans-latin.woff2' )
	);

}, 1 );
