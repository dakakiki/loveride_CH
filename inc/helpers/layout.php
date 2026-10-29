<?php
/**
 * Layout helpers: section wrapper attributes for ACF flexible layouts (Seiten-Layouts).
 *
 * @package loveride
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function lr_section_attrs( array $classes = array(), array $attrs = array() ): string {

	$layout  = (string) get_row_layout();
	$classes = array_merge( array( 'section', str_replace( '_', '-', $layout ) ), $classes );
	$scheme  = get_sub_field( 'farbschema' );

	if ( $scheme ) {
		$classes[] = 'scheme-' . sanitize_html_class( $scheme );
	}

	foreach ( array( 'abstand_oben' => 'pt', 'abstand_unten' => 'pb' ) as $field => $prefix ) {
		$value = get_sub_field( $field );

		if ( $value ) {
			$classes[] = $prefix . '-' . sanitize_html_class( $value );
		}
	}

	$anker = sanitize_title( (string) get_sub_field( 'anker' ) );

	if ( $anker !== '' ) {
		$attrs = array( 'id' => $anker ) + $attrs;
	}

	$attrs['class'] = implode( ' ', array_filter( $classes ) );

	$out = array();

	foreach ( $attrs as $name => $value ) {
		if ( $value === null || $value === false ) {
			continue;
		}

		$out[] = $value === true ? esc_attr( $name ) : sprintf( '%s="%s"', esc_attr( $name ), esc_attr( (string) $value ) );
	}

	return implode( ' ', $out );
}
