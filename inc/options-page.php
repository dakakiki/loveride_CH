<?php
/**
 * ACF options pages: Theme-Einstellungen (Event-Daten, Geteilte Inhalte).
 *
 * @package loveride
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'acf/init', function () {

	if ( ! function_exists( 'acf_add_options_page' ) ) {
		return;
	}

	acf_add_options_page( array(
		'page_title' => __( 'Theme-Einstellungen', 'loveride' ),
		'menu_title' => __( 'Theme-Einstellungen', 'loveride' ),
		'menu_slug'  => 'theme-einstellungen',
		'capability' => 'manage_options',
		'position'   => 59,
		'icon_url'   => 'dashicons-admin-generic',
		'redirect'   => false,
		'autoload'   => true,
	) );

	acf_add_options_sub_page( array(
		'page_title'  => __( 'Event-Daten', 'loveride' ),
		'menu_title'  => __( 'Event-Daten', 'loveride' ),
		'menu_slug'   => 'event-daten',
		'parent_slug' => 'theme-einstellungen',
		'capability'  => 'edit_pages',
		'autoload'    => true,
	) );

	acf_add_options_sub_page( array(
		'page_title'  => __( 'Geteilte Inhalte', 'loveride' ),
		'menu_title'  => __( 'Geteilte Inhalte', 'loveride' ),
		'menu_slug'   => 'geteilte-inhalte',
		'parent_slug' => 'theme-einstellungen',
		'capability'  => 'edit_pages',
	) );
} );
