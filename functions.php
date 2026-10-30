<?php
/**
 * Love Ride Switzerland – theme setup, assets, menus and includes.
 *
 * @package loveride
 * @author  Kommpass GmbH
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'LR_THEME_VERSION', wp_get_theme()->get( 'Version' ) );
define( 'LR_THEME_PATH', get_template_directory() );
define( 'LR_THEME_URI', get_template_directory_uri() );

function lr_dist_uri( string $rel ): string {
	return LR_THEME_URI . '/dist/' . ltrim( $rel, '/' );
}

function lr_dist_path( string $rel ): string {
	return LR_THEME_PATH . '/dist/' . ltrim( $rel, '/' );
}

function lr_dist_ver( string $rel ): ?int {
	$path = lr_dist_path( $rel );

	return file_exists( $path ) ? filemtime( $path ) : null;
}

function lr_pick_dist( string $min_rel, string $rel ): string {
	return file_exists( lr_dist_path( $min_rel ) ) ? $min_rel : $rel;
}

function lr_is_frontend(): bool {
	return ! is_admin() && ! wp_doing_ajax() && ! wp_is_json_request();
}

add_action( 'wp_enqueue_scripts', function () {

	if ( ! lr_is_frontend() ) {
		return;
	}

	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_style( 'global-styles' );
	wp_dequeue_style( 'classic-theme-styles' );

}, 100 );

add_action( 'wp_enqueue_scripts', function () {

	if ( is_admin() ) {
		return;
	}

	$css = lr_pick_dist( 'css/style.min.css', 'css/style.css' );

	wp_enqueue_style( 'lr-style', lr_dist_uri( $css ), array(), lr_dist_ver( $css ) );

	$scripts = array(
		'lr-gsap'   => array( 'js/gsap.min.js', array() ),
		'lr-custom' => array( 'js/custom.min.js', array( 'lr-gsap' ) ),
	);

	foreach ( $scripts as $handle => list( $rel, $deps ) ) {
		wp_enqueue_script( $handle, lr_dist_uri( $rel ), $deps, lr_dist_ver( $rel ), array(
			'in_footer' => true,
			'strategy'  => 'defer',
		) );
	}

	wp_localize_script( 'lr-custom', 'lrTheme', array(
		'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
		'themeUri' => LR_THEME_URI,
	) );

	if ( lr_logo_sponsors() ) {
		wp_script_add_data( 'jquery-core', 'strategy', 'defer' );
		wp_script_add_data( 'jquery-migrate', 'strategy', 'defer' );

		$slider_scripts = array(
			'slick'             => array( 'js/slick.min.js', array( 'jquery' ) ),
			'lr-partner-slider' => array( 'js/partner-slider.min.js', array( 'jquery', 'slick' ) ),
		);

		foreach ( $slider_scripts as $handle => list( $rel, $deps ) ) {
			wp_enqueue_script( $handle, lr_dist_uri( $rel ), $deps, lr_dist_ver( $rel ), array(
				'in_footer' => true,
				'strategy'  => 'defer',
			) );
		}
	}

}, 20 );

function lr_layout_scripts(): array {

	return apply_filters( 'lr_layout_scripts', array() );
}

add_action( 'wp_enqueue_scripts', function () {

	if ( is_admin() ) {
		return;
	}

	$scripts = lr_layout_scripts();
	$present = lr_detect_layouts_on_page();

	if ( empty( $scripts ) || empty( $present ) ) {
		return;
	}

	foreach ( $scripts as $layout => $cfg ) {

		if ( ! in_array( $layout, $present, true ) ) {
			continue;
		}

		$rel = lr_pick_dist( $cfg['min'], $cfg['file'] );

		wp_enqueue_script( $cfg['handle'] ?? $layout, lr_dist_uri( $rel ), $cfg['deps'] ?? array(), lr_dist_ver( $rel ), array(
			'in_footer' => true,
			'strategy'  => 'defer',
		) );
	}

}, 25 );

add_action( 'after_setup_theme', function () {

	load_theme_textdomain( 'loveride', LR_THEME_PATH . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'html5', array(
		'search-form',
		'gallery',
		'caption',
		'style',
		'script',
	) );

	register_nav_menus( array(
		'menu-main'   => __( 'Hauptmenü', 'loveride' ),
		'menu-topbar' => __( 'Topbar / Schnelllinks', 'loveride' ),
		'menu-footer' => __( 'Footer-Spalten', 'loveride' ),
		'menu-legal'  => __( 'Footer unten', 'loveride' ),
	) );
} );

add_action( 'init', function () {

	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
} );

add_filter( 'excerpt_more', '__return_empty_string' );

require_once LR_THEME_PATH . '/inc/helpers/acf.php';
require_once LR_THEME_PATH . '/inc/helpers/layout.php';
require_once LR_THEME_PATH . '/inc/helpers/fonts.php';
require_once LR_THEME_PATH . '/inc/helpers/format.php';
require_once LR_THEME_PATH . '/inc/helpers/svg.php';
require_once LR_THEME_PATH . '/inc/helpers/wp_editor.php';
require_once LR_THEME_PATH . '/inc/helpers/wp_editor_colors.php';
require_once LR_THEME_PATH . '/inc/helpers/debug.php';

require_once LR_THEME_PATH . '/inc/options-page.php';
require_once LR_THEME_PATH . '/inc/event.php';
require_once LR_THEME_PATH . '/inc/menu.php';
require_once LR_THEME_PATH . '/inc/forms.php';
require_once LR_THEME_PATH . '/inc/icons.php';
require_once LR_THEME_PATH . '/inc/live.php';
require_once LR_THEME_PATH . '/inc/anreise.php';
require_once LR_THEME_PATH . '/inc/gut-zu-wissen.php';
require_once LR_THEME_PATH . '/inc/news.php';
require_once LR_THEME_PATH . '/inc/medien.php';
require_once LR_THEME_PATH . '/inc/jahrgang.php';
require_once LR_THEME_PATH . '/inc/popups.php';
require_once LR_THEME_PATH . '/inc/read-aloud.php';
require_once LR_THEME_PATH . '/inc/admin.php';
require_once LR_THEME_PATH . '/inc/layouts.php';
