<?php
/**
 * Editors: block editor (pages, News) with allowed core blocks only, no patterns or block directory, paragraph style "Hinweis"; TinyMCE (ACF WYSIWYG) formats without H1.
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

add_filter( 'allowed_block_types_all', function () {

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
} );

add_action( 'init', function () {

	register_block_style( 'core/paragraph', array(
		'name'  => 'hinweis',
		'label' => 'Hinweis',
	) );
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
