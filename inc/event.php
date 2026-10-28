<?php
/**
 * Event-Daten: access to the next Love Ride (date, times, place) and the Topbar text (Theme-Einstellungen → Topbar).
 *
 * @package loveride
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function lr_event( string $key, $default = '' ) {

	return lr_get_option( 'event_' . $key, $default );
}

function lr_event_date( string $style = 'short' ): string {

	return lr_format_date( lr_event( 'datum' ), $style );
}

function lr_event_opening_hours(): string {

	return lr_format_time_range( lr_event( 'offen_von' ), lr_event( 'offen_bis' ) );
}

/**
 * @param string $variant full "Save the Date: So, 2. Mai 2027 · Ort · 8.00 bis 17.00 Uhr" | medium (without hours) | short "So, 2. Mai 2027 · Ort"
 */
function lr_topbar_text( string $variant = 'full', bool $custom = true ): string {

	$text  = $custom ? trim( (string) lr_get_option( 'topbar_text', '' ) ) : '';
	$date  = lr_event_date();
	$place = trim( (string) lr_event( 'ort' ) );

	if ( $variant === 'short' && $date ) {
		return implode( ' · ', array_filter( array( $date, $place ) ) );
	}

	if ( $text !== '' ) {
		return $text;
	}

	if ( ! $date ) {
		return '';
	}

	$parts = array( $date, $place );

	if ( $variant === 'full' ) {
		$parts[] = lr_event_opening_hours();
	}

	return trim( trim( (string) lr_get_option( 'topbar_praefix', '' ) ) . ' ' . implode( ' · ', array_filter( $parts ) ) );
}

add_filter( 'acf/prepare_field/name=topbar_text', function ( $field ) {

	$field['placeholder'] = lr_topbar_text( 'full', false );

	return $field;
} );
