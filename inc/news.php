<?php
/**
 * News: category, date and teaser helpers, URL of the News page (page with layout_news_archiv), further posts, YouTube embeds without cookies, News script on single posts.
 *
 * @package loveride
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function lr_news_category( int $post_id = 0 ): ?WP_Term {

	$terms = get_the_terms( $post_id ?: get_the_ID(), 'news_kategorie' );

	return $terms && ! is_wp_error( $terms ) ? $terms[0] : null;
}

function lr_news_date( int $post_id = 0 ): string {

	return lr_format_date( get_the_date( 'Ymd', $post_id ?: get_the_ID() ), 'plain' );
}

function lr_news_teaser( int $post_id = 0 ): string {

	$post_id = $post_id ?: get_the_ID();
	$text    = has_excerpt( $post_id ) ? get_the_excerpt( $post_id ) : (string) get_field( 'lead', $post_id );

	return trim( wp_strip_all_tags( $text ) );
}

function lr_news_page_url(): string {

	static $url = null;

	if ( $url === null ) {
		$pages = get_posts( array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery
				array(
					'key'     => 'layouts',
					'value'   => '"layout_news_archiv"',
					'compare' => 'LIKE',
				),
			),
		) );

		$url = $pages ? (string) get_permalink( $pages[0] ) : home_url( '/' );
	}

	return $url;
}

function lr_news_more( int $post_id, int $count = 3 ): array {

	$ids = array_map( 'intval', (array) ( get_field( 'weitere_beitraege', $post_id, false ) ?: array() ) );
	$ids = array_values( array_filter( $ids, fn( $id ) => $id !== $post_id && get_post_status( $id ) === 'publish' ) );

	if ( count( $ids ) < $count ) {
		$ids = array_merge( $ids, get_posts( array(
			'post_type'      => 'news',
			'post_status'    => 'publish',
			'posts_per_page' => $count - count( $ids ),
			'post__not_in'   => array_merge( $ids, array( $post_id ) ),
			'fields'         => 'ids',
		) ) );
	}

	return array_slice( $ids, 0, $count );
}

add_filter( 'embed_oembed_html', function ( $html ) {

	return str_replace( array( 'www.youtube.com/embed', 'youtube.com/embed' ), 'www.youtube-nocookie.com/embed', (string) $html );
} );

add_action( 'wp_enqueue_scripts', function () {

	if ( ! is_singular( 'news' ) ) {
		return;
	}

	$rel = lr_pick_dist( 'js/news.min.js', 'js/news.min.js' );

	wp_enqueue_script( 'lr-news', lr_dist_uri( $rel ), array(), lr_dist_ver( $rel ), array(
		'in_footer' => true,
		'strategy'  => 'defer',
	) );
}, 25 );
