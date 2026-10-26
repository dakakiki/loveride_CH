<?php
/**
 * ACF WYSIWYG: text color picker limited to the Love Ride palette (mirrors abstracts/_colors.scss).
 *
 * @package loveride
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function lr_editor_text_colours(): array {

	$palette = apply_filters( 'lr_editor_text_colours', array(
		'1A1A1A' => __( 'Text', 'loveride' ),
		'555555' => __( 'Grau', 'loveride' ),
		'CE0E2D' => __( 'Rot', 'loveride' ),
		'A50B24' => __( 'Rot Dunkel', 'loveride' ),
	) );

	$flat = array();

	foreach ( $palette as $hex => $label ) {
		$flat[] = $hex;
		$flat[] = $label;
	}

	return $flat;
}

add_filter( 'tiny_mce_before_init', function ( array $settings ): array {

	foreach ( array( 'toolbar4', 'toolbar3', 'toolbar2', 'toolbar1' ) as $row ) {

		if ( empty( $settings[ $row ] ) ) {
			continue;
		}

		if ( strpos( $settings[ $row ], 'forecolor' ) === false ) {
			$settings[ $row ] .= ',forecolor';
		}

		break;
	}

	$colours = lr_editor_text_colours();

	$settings['textcolor_map']  = wp_json_encode( $colours );
	$settings['textcolor_rows'] = 1;
	$settings['textcolor_cols'] = (int) ( count( $colours ) / 2 );
	$settings['custom_colors']  = false;

	return $settings;
} );

add_filter( 'tiny_mce_plugins', function ( array $plugins ): array {

	if ( ! in_array( 'textcolor', $plugins, true ) ) {
		$plugins[] = 'textcolor';
	}

	return $plugins;
} );
