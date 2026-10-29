<?php
/**
 * Live-Hinweise: content from Geteilte Inhalte (tab Live-Hinweise), info line from Event-Daten and "Zuletzt aktualisiert" timestamp; used by layout_live (Home, Anreise).
 *
 * @package loveride
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const LR_LIVE_UPDATED_OPTION = 'lr_live_aktualisiert';

function lr_live(): array {

	return (array) lr_get_option( 'geteilt_live', array() );
}

function lr_live_active(): bool {

	return ! empty( lr_live()['aktiv'] );
}

function lr_live_updated(): ?int {

	$timestamp = (int) get_option( LR_LIVE_UPDATED_OPTION, 0 );

	return $timestamp > 0 ? $timestamp : null;
}

function lr_live_updated_text(): string {

	$timestamp = lr_live_updated();

	if ( ! $timestamp ) {
		return '';
	}

	$time = lr_format_time( $timestamp );

	return wp_date( 'Y-m-d', $timestamp ) === wp_date( 'Y-m-d' ) ? $time : lr_format_date( $timestamp, 'plain' ) . ', ' . $time;
}

function lr_live_infozeile(): string {

	$hours = lr_event_opening_hours();
	$start = lr_event_value( 'ride_out_start' );

	return implode( ' · ', array_filter( array(
		lr_event_date(),
		$hours ? sprintf( __( 'Gelände offen %s', 'loveride' ), $hours ) : '',
		$start ? sprintf( __( 'Ride-out-Start %s', 'loveride' ), $start ) : '',
	) ) );
}

add_action( 'acf/init', function () {

	$before = null;

	add_action( 'acf/save_post', function ( $post_id ) use ( &$before ) {

		if ( $post_id === 'options' && ( $_GET['page'] ?? '' ) === 'geteilte-inhalte' ) {
			$before = maybe_serialize( get_field( 'geteilt_live', 'option', false ) );
		}
	}, 5 );

	add_action( 'acf/save_post', function ( $post_id ) use ( &$before ) {

		if ( $before === null || $post_id !== 'options' ) {
			return;
		}

		if ( maybe_serialize( get_field( 'geteilt_live', 'option', false ) ) !== $before ) {
			update_option( LR_LIVE_UPDATED_OPTION, time(), false );
		}

		$before = null;
	}, 20 );
} );
