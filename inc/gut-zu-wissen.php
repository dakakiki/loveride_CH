<?php
/**
 * Gut zu wissen (Geteilte Inhalte → Gut zu wissen): points with a stable Kennung (filled from the title when empty), choices for the point selection in layout_gut_zu_wissen.
 *
 * @package loveride
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const LR_GUT_ZU_WISSEN_AUSWAHL = 'field_7a3237457f8fd';

function lr_gut_zu_wissen_punkte(): array {

	$data   = (array) lr_get_option( 'geteilt_gut_zu_wissen', array() );
	$punkte = array();

	foreach ( (array) ( ( $data['punkte'] ?? null ) ?: array() ) as $punkt ) {
		$titel = trim( (string) ( $punkt['titel'] ?? '' ) );

		if ( $titel === '' ) {
			continue;
		}

		$punkte[] = array(
			'key'   => sanitize_title( (string) ( $punkt['kennung'] ?? '' ) ) ?: sanitize_title( $titel ),
			'icon'  => (string) ( $punkt['icon'] ?? '' ),
			'titel' => $titel,
			'text'  => trim( (string) ( $punkt['text'] ?? '' ) ),
		);
	}

	return $punkte;
}

add_action( 'acf/save_post', function ( $post_id ) {

	if ( $post_id !== 'options' ) {
		return;
	}

	$count = (int) get_option( 'options_geteilt_gut_zu_wissen_punkte' );

	for ( $i = 0; $i < $count; $i++ ) {
		$kennung = "options_geteilt_gut_zu_wissen_punkte_{$i}_kennung";

		if ( trim( (string) get_option( $kennung ) ) === '' ) {
			update_option( $kennung, sanitize_title( (string) get_option( "options_geteilt_gut_zu_wissen_punkte_{$i}_titel" ) ) );
		}
	}
}, 20 );

add_filter( 'acf/prepare_field/key=' . LR_GUT_ZU_WISSEN_AUSWAHL, function ( $field ) {

	$field['choices'] = wp_list_pluck( lr_gut_zu_wissen_punkte(), 'titel', 'key' );

	return $field;
} );
