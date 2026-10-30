<?php
/**
 * Seiten-Layouts (ACF flexible content "layouts"): per-layout scripts, admin row titles, unique anchors per page.
 *
 * @package loveride
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const LR_LAYOUTS_FIELD = 'field_277042eada568';
const LR_ANKER_FIELD   = 'field_6ffe6fe9ad61b';

add_filter( 'lr_layout_scripts', function ( array $scripts ): array {

	$hero_slider = array(
		'handle' => 'lr-hero-slider',
		'file'   => 'js/hero-slider.min.js',
		'min'    => 'js/hero-slider.min.js',
		'deps'   => array( 'lr-gsap' ),
	);

	$scripts['layout_hero']      = $hero_slider;
	$scripts['layout_page_hero'] = $hero_slider;

	$scripts['layout_zahlenband'] = array(
		'handle' => 'lr-zahlenband',
		'file'   => 'js/zahlenband.min.js',
		'min'    => 'js/zahlenband.min.js',
		'deps'   => array( 'lr-gsap' ),
	);

	$karten = array(
		'handle' => 'lr-karten',
		'file'   => 'js/karten.min.js',
		'min'    => 'js/karten.min.js',
		'deps'   => array(),
	);

	$scripts['layout_gelaendeplan'] = $karten;
	$scripts['layout_anreise']      = $karten;

	$scripts['layout_programm'] = array(
		'handle' => 'lr-programm',
		'file'   => 'js/programm.min.js',
		'min'    => 'js/programm.min.js',
		'deps'   => array(),
	);

	$scripts['layout_logo_wand'] = array(
		'handle' => 'lr-logo-wand',
		'file'   => 'js/logo-wand.min.js',
		'min'    => 'js/logo-wand.min.js',
		'deps'   => array(),
	);

	$scripts['layout_news_archiv'] = array(
		'handle' => 'lr-news',
		'file'   => 'js/news.min.js',
		'min'    => 'js/news.min.js',
		'deps'   => array(),
	);

	$scripts['layout_galerie'] = array(
		'handle' => 'lr-galerie',
		'file'   => 'js/galerie.min.js',
		'min'    => 'js/galerie.min.js',
		'deps'   => array(),
	);

	$scripts['layout_event_teaser'] = array(
		'handle' => 'lr-countdown',
		'file'   => 'js/countdown.min.js',
		'min'    => 'js/countdown.min.js',
		'deps'   => array(),
	);

	$scripts['layout_faq'] = array(
		'handle' => 'lr-faq',
		'file'   => 'js/faq.min.js',
		'min'    => 'js/faq.min.js',
		'deps'   => array(),
	);

	return $scripts;
} );

add_filter( 'acf/fields/flexible_content/layout_title/name=layouts', function ( $title ) {

	$heading = get_sub_field( 'titel' );

	if ( ! $heading && get_row_layout() === 'layout_hero' ) {
		$slides  = get_sub_field( 'slides' );
		$heading = $slides[0]['inhalt']['titel'] ?? '';
	}

	$meta = array_filter( array(
		trim( wp_strip_all_tags( str_replace( '<br />', ' ', (string) $heading ) ) ),
		(string) get_sub_field( 'anker' ) !== '' ? '#' . get_sub_field( 'anker' ) : '',
		get_row_layout() === 'layout_live' && ! lr_live_active() ? __( 'ausgeblendet, Live-Hinweise sind aus', 'loveride' ) : '',
	) );

	if ( $meta ) {
		$title .= ' <span class="lr-layout-meta">– ' . esc_html( implode( ' · ', $meta ) ) . '</span>';
	}

	return $title;
} );

function lr_find_values_by_key( $data, string $key ): array {

	$found = array();

	if ( ! is_array( $data ) ) {
		return $found;
	}

	foreach ( $data as $k => $value ) {
		if ( is_string( $k ) && substr( $k, -strlen( $key ) ) === $key && is_scalar( $value ) ) {
			$found[] = (string) $value;
		} elseif ( is_array( $value ) ) {
			$found = array_merge( $found, lr_find_values_by_key( $value, $key ) );
		}
	}

	return $found;
}

add_action( 'acf/validate_save_post', function () {

	$rows = $_POST['acf'][ LR_LAYOUTS_FIELD ] ?? null; // phpcs:ignore WordPress.Security.NonceVerification

	if ( ! is_array( $rows ) ) {
		return;
	}

	$seen = array();

	foreach ( $rows as $row ) {
		foreach ( lr_find_values_by_key( $row, LR_ANKER_FIELD ) as $anker ) {
			$anker = sanitize_title( ltrim( wp_unslash( $anker ), '#' ) );

			if ( $anker === '' ) {
				continue;
			}

			if ( isset( $seen[ $anker ] ) ) {
				/* translators: %s: anchor */
				acf_add_validation_error( '', sprintf( __( 'Der Anker «#%s» wird auf dieser Seite mehrfach verwendet.', 'loveride' ), $anker ) );
			}

			$seen[ $anker ] = true;
		}
	}
} );
