<?php
/**
 * Editors: block editor (pages, News) with allowed core blocks only (News: text, headings H2–H4, lists, images, YouTube), no patterns or block directory, paragraph style "Hinweis", list style "Hervorgehoben"; TinyMCE (ACF WYSIWYG) formats without H1.
 *
 * @package loveride
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'after_setup_theme', function () {
	remove_theme_support( 'core-block-patterns' );
} );

add_filter( 'should_load_remote_block_patterns', '__return_false' );

add_action( 'plugins_loaded', function () {
	remove_action( 'enqueue_block_editor_assets', 'wp_enqueue_editor_block_directory_assets' );
} );

add_filter( 'allowed_block_types_all', function ( $allowed, $context ) {

	if ( ( $context->post->post_type ?? '' ) === 'news' ) {
		return array(
			'core/paragraph',
			'core/heading',
			'core/list',
			'core/list-item',
			'core/image',
			'core/embed',
			'core/missing',
		);
	}

	return array(
		'core/paragraph',
		'core/heading',
		'core/list',
		'core/list-item',
		'core/quote',
		'core/image',
		'core/embed',
		'core/buttons',
		'core/button',
		'core/spacer',
		'core/missing',
	);
}, 10, 2 );

add_filter( 'register_block_type_args', function ( array $args, string $name ): array {

	if ( $name === 'core/heading' ) {
		$args['attributes']['levelOptions']['default'] = array( 2, 3, 4 );
	}

	return $args;
}, 10, 2 );

add_action( 'init', function () {

	register_block_style( 'core/paragraph', array(
		'name'  => 'hinweis',
		'label' => 'Hinweis',
	) );

	register_block_style( 'core/list', array(
		'name'  => 'hervorgehoben',
		'label' => 'Hervorgehoben',
	) );
} );

add_action( 'enqueue_block_editor_assets', function () {

	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

	if ( ! $screen || $screen->post_type !== 'news' ) {
		return;
	}

	wp_add_inline_script( 'wp-blocks', 'wp.hooks.addFilter( "blocks.registerBlockType", "loveride/news-embeds", function ( settings, name ) { return name === "core/embed" ? Object.assign( {}, settings, { variations: ( settings.variations || [] ).filter( function ( variation ) { return variation.name === "youtube"; } ) } ) : settings; } );' );
} );

add_filter( 'tiny_mce_before_init', function ( array $init ): array {

	$init['block_formats'] = 'Absatz=p;Überschrift 2=h2;Überschrift 3=h3;Überschrift 4=h4';

	return $init;
} );

add_action( 'admin_head', function () {

	echo '<style>
		.editor-inserter__patterns,
		.block-editor-block-patterns-list,
		.components-tab-panel__tabs button[id*="pattern"],
		.block-editor-inserter__panel-header button[id*="pattern"] {
			display: none !important;
		}
	</style>';
} );
