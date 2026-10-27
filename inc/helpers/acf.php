<?php
/**
 * ACF helpers: options access, layout detection, layout rendering, responsive images.
 *
 * @package loveride
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function lr_get_option( string $name, $default = null ) {

	if ( ! function_exists( 'get_field' ) ) {
		return $default;
	}

	$value = get_field( $name, 'option' );

	return ( $value === null || $value === '' || $value === false ) ? $default : $value;
}

function lr_detect_layouts_on_page( $post_id = null ): array {

	if ( ! function_exists( 'get_field' ) ) {
		return array();
	}

	$post_id = $post_id ?? get_queried_object_id();

	if ( empty( $post_id ) ) {
		return array();
	}

	$rows = get_field( 'layouts', $post_id, false );

	if ( empty( $rows ) || ! is_array( $rows ) ) {
		return array();
	}

	$layouts = array();

	foreach ( $rows as $row ) {
		if ( ! empty( $row['acf_fc_layout'] ) ) {
			$layouts[] = $row['acf_fc_layout'];
		}
	}

	return array_values( array_unique( $layouts ) );
}

function lr_page_has_layout( $layouts, $post_id = null ): bool {

	return (bool) array_intersect( (array) $layouts, lr_detect_layouts_on_page( $post_id ) );
}

function lr_render_layout( string $layout ): void {

	$layout = sanitize_key( $layout );
	$part   = locate_template( "template_parts/layouts/{$layout}.php" );

	if ( $part ) {
		load_template( $part, false );
		return;
	}

	if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
		trigger_error( "Missing layout template: template_parts/layouts/{$layout}.php", E_USER_WARNING );
	}
}

add_filter( 'acf/update_value/name=kennung', function ( $value ) {
	return sanitize_title( (string) $value );
} );

add_filter( 'acf/update_value/name=anker', function ( $value ) {
	return sanitize_title( ltrim( (string) $value, '#' ) );
} );

function lr_acf_image( $image, string $size = 'large', array $attrs = array() ): string {

	if ( empty( $image ) || empty( $image['ID'] ) ) {
		return '';
	}

	if ( ( $image['mime_type'] ?? '' ) === 'image/svg+xml' ) {

		$out = '<img src="' . esc_url( $image['url'] ) . '" alt="' . esc_attr( $attrs['alt'] ?? $image['alt'] ) . '"';

		if ( ! empty( $image['width'] ) && ! empty( $image['height'] ) ) {
			$out .= ' width="' . esc_attr( $image['width'] ) . '" height="' . esc_attr( $image['height'] ) . '"';
		}

		unset( $attrs['alt'] );

		foreach ( $attrs as $key => $value ) {
			$out .= ' ' . esc_attr( $key ) . '="' . esc_attr( $value ) . '"';
		}

		return $out . '>';
	}

	return wp_get_attachment_image( $image['ID'], $size, false, $attrs );
}
