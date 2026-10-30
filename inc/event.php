<?php
/**
 * Event-Daten: access to the next Love Ride (date, times, place, countdown start) and the Topbar text (Theme-Einstellungen → Topbar).
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

function lr_event_start(): ?DateTimeImmutable {

	$date = (string) lr_event( 'datum' );

	if ( ! preg_match( '/^\d{8}$/', $date ) ) {
		return null;
	}

	$time  = (string) lr_event( 'offen_von' ) ?: '00:00';
	$start = DateTimeImmutable::createFromFormat( 'Ymd H:i', $date . ' ' . substr( $time, 0, 5 ), wp_timezone() );

	return $start ?: null;
}

function lr_event_opening_hours(): string {

	return lr_format_time_range( lr_event( 'offen_von' ), lr_event( 'offen_bis' ) );
}

function lr_event_value( string $type ): string {

	switch ( $type ) {
		case 'datum':
			return lr_event_date();
		case 'zeit':
			return lr_event_opening_hours();
		case 'offen_von':
		case 'ride_out_start':
		case 'ride_out_ende':
			return lr_format_time( lr_event( $type ) );
		case 'ort':
			return trim( (string) lr_event( 'ort' ) );
	}

	return '';
}

/**
 * Eckdaten rows (Page-Hero, Medienmitteilungen info box): typ = Event-Daten value or „eigen“, vor = text before an Event-Daten value.
 *
 * @return array<int, array{label: string, wert: string}>
 */
function lr_eckdaten( array $rows ): array {

	$facts = array();

	foreach ( $rows as $row ) {
		$type  = $row['typ'] ?? 'eigen';
		$value = $type === 'eigen' ? trim( (string) ( $row['wert'] ?? '' ) ) : lr_event_value( $type );

		if ( $value !== '' && $type !== 'eigen' && ! empty( $row['vor'] ) ) {
			$value = trim( $row['vor'] ) . ' ' . $value;
		}

		if ( $value === '' || empty( $row['label'] ) ) {
			continue;
		}

		$facts[] = array(
			'label' => (string) $row['label'],
			'wert'  => $value,
		);
	}

	return $facts;
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
