<?php
/**
 * Live-Modus: active state and "Zuletzt aktualisiert" timestamp on the Live-Modus options page.
 *
 * @package loveride
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const LR_LIVE_UPDATED_OPTION = 'lr_live_aktualisiert';

const LR_LIVE_UPDATED_FIELD = 'field_56237569b73ad';

function lr_live_active(): bool {

	return (bool) lr_get_option( 'live_aktiv', false );
}

function lr_live_updated(): ?int {

	$timestamp = (int) get_option( LR_LIVE_UPDATED_OPTION, 0 );

	return $timestamp > 0 ? $timestamp : null;
}

add_action( 'acf/save_post', function ( $post_id ) {

	if ( $post_id !== 'options' || ( $_GET['page'] ?? '' ) !== 'live-modus' ) {
		return;
	}

	update_option( LR_LIVE_UPDATED_OPTION, time(), false );

}, 20 );

add_filter( 'acf/prepare_field/key=' . LR_LIVE_UPDATED_FIELD, function ( $field ) {

	$timestamp = lr_live_updated();

	$field['message'] = $timestamp
		? lr_format_date( $timestamp, 'long' ) . ', ' . lr_format_time( wp_date( 'H:i', $timestamp ) )
		: '–';

	return $field;
} );
