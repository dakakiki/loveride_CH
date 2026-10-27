<?php
/**
 * Menus: nested item tree with ACF mega menu fields (menu-main), flat item lists, logo and header CTA helpers.
 *
 * @package loveride
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function lr_menu_items( string $location ): array {

	$locations = get_nav_menu_locations();

	if ( empty( $locations[ $location ] ) ) {
		return array();
	}

	return wp_get_nav_menu_items( $locations[ $location ] ) ?: array();
}

function lr_menu_tree( string $location ): array {

	$current = (int) get_queried_object_id();
	$items   = lr_menu_items( $location );
	$nodes   = array();
	$tree    = array();

	foreach ( $items as $item ) {
		$nodes[ $item->ID ] = array(
			'id'         => (int) $item->ID,
			'title'      => $item->title,
			'url'        => $item->url,
			'target'     => $item->target,
			'current'    => $item->type === 'post_type' && (int) $item->object_id === $current,
			'untertitel' => function_exists( 'get_field' ) ? (string) get_field( 'untertitel', $item->ID ) : '',
			'intro'      => function_exists( 'get_field' ) ? (string) get_field( 'intro_text', $item->ID ) : '',
			'box'        => function_exists( 'get_field' ) ? (array) get_field( 'hervorgehoben', $item->ID ) : array(),
			'children'   => array(),
			'parent'     => (int) $item->menu_item_parent,
		);
	}

	$children = array();

	foreach ( $nodes as $node ) {
		if ( $node['parent'] ) {
			$children[ $node['parent'] ][] = $node;
		}
	}

	foreach ( $nodes as $id => $node ) {

		if ( $node['parent'] ) {
			continue;
		}

		$node['children'] = $children[ $id ] ?? array();
		$node['active']   = $node['current'] || (bool) array_filter( $node['children'], fn( $child ) => $child['current'] );

		$tree[] = $node;
	}

	return $tree;
}

function lr_logo( bool $light = false, array $attrs = array() ): string {

	$option = lr_get_option( $light ? 'logo_hell' : 'logo' );
	$alt    = get_bloginfo( 'name' );

	if ( empty( $option ) ) {
		return '<span class="logo-text">' . esc_html( $alt ) . '</span>';
	}

	if ( empty( $option['alt'] ) ) {
		$attrs['alt'] = $alt;
	}

	return lr_acf_image( $option, 'medium', $attrs );
}

function lr_link_attrs( array $link ): string {

	$target = ( $link['target'] ?? '' ) ?: '_self';

	return sprintf(
		'href="%s" target="%s"%s',
		esc_url( $link['url'] ?? '' ),
		esc_attr( $target ),
		$target === '_blank' ? ' rel="noopener"' : ''
	);
}

function lr_logo_sponsors( int $limit = 10 ): array {

	$query = new WP_Query( array(
		'post_type'              => 'sponsor',
		'post_status'            => 'publish',
		'posts_per_page'         => $limit,
		'orderby'                => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
		'meta_key'               => 'in_logo_leiste',
		'meta_value'             => '1',
		'no_found_rows'          => true,
		'update_post_term_cache' => false,
	) );

	$sponsors = array();

	foreach ( $query->posts as $post ) {
		$logo = function_exists( 'get_field' ) ? get_field( 'logo', $post->ID ) : null;

		if ( empty( $logo ) ) {
			continue;
		}

		$sponsors[] = array(
			'title'   => get_the_title( $post ),
			'logo'    => $logo,
			'website' => (string) get_field( 'website', $post->ID ),
		);
	}

	return $sponsors;
}

function lr_social_label( string $platform ): string {

	$labels = array(
		'facebook'  => 'Facebook',
		'instagram' => 'Instagram',
		'youtube'   => 'YouTube',
		'tiktok'    => 'TikTok',
		'linkedin'  => 'LinkedIn',
	);

	return $labels[ $platform ] ?? ucfirst( $platform );
}

function lr_header_buttons(): array {

	return array_filter( array(
		'tickets' => lr_get_option( 'button_tickets' ),
		'spenden' => lr_get_option( 'button_spenden' ),
	), fn( $link ) => ! empty( $link['url'] ) );
}
