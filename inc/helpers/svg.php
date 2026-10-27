<?php
/**
 * SVG: inline output with unique IDs, SVG upload for administrators.
 *
 * @package loveride
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function lr_inline_svg( string $url, string $prefix = '' ): string {

	$upload_dir = wp_get_upload_dir();

	if ( strpos( $url, $upload_dir['baseurl'] ) === 0 ) {
		$file_path = str_replace( $upload_dir['baseurl'], $upload_dir['basedir'], $url );
	} else {
		$file_path = str_replace( LR_THEME_URI, LR_THEME_PATH, $url );
	}

	if ( ! file_exists( $file_path ) ) {
		return '';
	}

	$svg = file_get_contents( $file_path );

	if ( ! $svg ) {
		return '';
	}

	$svg = preg_replace( '/<\?xml.*?\?>\s*/', '', $svg );

	if ( $prefix !== '' && preg_match_all( '/\sid="([^"]+)"/', $svg, $m ) ) {

		foreach ( array_unique( $m[1] ) as $id ) {
			$new = $prefix . '-' . $id;

			$svg = str_replace(
				array( 'id="' . $id . '"', 'url(#' . $id . ')', 'href="#' . $id . '"' ),
				array( 'id="' . $new . '"', 'url(#' . $new . ')', 'href="#' . $new . '"' ),
				$svg
			);
		}
	}

	return $svg;
}

function lr_ui_icon( string $name, string $class = '' ): string {

	$file = LR_THEME_PATH . '/assets/images/ui/' . sanitize_key( $name ) . '.svg';

	if ( ! file_exists( $file ) ) {
		return '';
	}

	return (string) preg_replace(
		'/<svg\b/',
		'<svg class="icon ' . esc_attr( trim( 'icon--' . $name . ' ' . $class ) ) . '" aria-hidden="true" focusable="false"',
		file_get_contents( $file ),
		1
	);
}

add_filter( 'upload_mimes', function ( $mimes ) {

	if ( current_user_can( 'manage_options' ) ) {
		$mimes['svg'] = 'image/svg+xml';
	}

	return $mimes;
} );

add_filter( 'wp_check_filetype_and_ext', function ( $data, $file, $filename, $mimes ) {

	if ( ! current_user_can( 'manage_options' ) ) {
		return $data;
	}

	$filetype = wp_check_filetype( $filename, $mimes );

	if ( $filetype['ext'] === 'svg' ) {
		$data['ext']  = 'svg';
		$data['type'] = 'image/svg+xml';
	}

	return $data;
}, 10, 4 );

add_filter( 'wp_prepare_attachment_for_js', function ( $response ) {

	if ( $response['mime'] === 'image/svg+xml' ) {
		$response['sizes'] = array(
			'full' => array(
				'url'         => $response['url'],
				'width'       => 0,
				'height'      => 0,
				'orientation' => 'portrait',
			),
		);
	}

	return $response;
} );
