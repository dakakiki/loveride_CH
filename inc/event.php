<?php
/**
 * Event-Daten: access to the next Love Ride (date, times, place) and derived texts (Topbar).
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
function lr_topbar_text( string $variant = 'full' ): string {

	$custom = trim( (string) lr_event( 'topbar_text' ) );
	$date   = lr_event_date();
	$place  = trim( (string) lr_event( 'ort' ) );

	if ( $variant === 'short' && $date ) {
		return implode( ' · ', array_filter( array( $date, $place ) ) );
	}

	if ( $custom !== '' ) {
		return $custom;
	}

	if ( ! $date ) {
		return '';
	}

	$parts = array( $date, $place );

	if ( $variant === 'full' ) {
		$parts[] = lr_event_opening_hours();
	}

	return __( 'Save the Date:', 'loveride' ) . ' ' . implode( ' · ', array_filter( $parts ) );
}
