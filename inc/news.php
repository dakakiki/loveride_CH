<?php
/**
 * News: category, date, author, teaser (excerpt) and tag helpers, URL of the News page (page with layout_news_archiv), further posts (3 random with a shared tag, then same category), YouTube embeds without cookies, News script on single posts.
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

	return has_excerpt( $post_id ) ? trim( wp_strip_all_tags( get_the_excerpt( $post_id ) ) ) : '';
}

function lr_news_author( int $post_id = 0 ): string {

	$author = (int) get_post_field( 'post_author', $post_id ?: get_the_ID() );

	return $author ? trim( (string) get_the_author_meta( 'display_name', $author ) ) : '';
}

function lr_news_tags( int $post_id = 0 ): array {

	$terms = get_the_terms( $post_id ?: get_the_ID(), 'post_tag' );

	if ( ! $terms || is_wp_error( $terms ) ) {
		return array();
	}

	return array_map( fn( $term ) => '#' . ltrim( $term->name, '#' ), $terms );
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

	$ids = array();

	foreach ( array( 'post_tag', 'news_kategorie' ) as $taxonomy ) {
		$terms = wp_get_post_terms( $post_id, $taxonomy, array( 'fields' => 'ids' ) );

		if ( count( $ids ) >= $count || ! $terms || is_wp_error( $terms ) ) {
			continue;
		}

		$ids = array_merge( $ids, get_posts( array(
			'post_type'      => 'news',
			'post_status'    => 'publish',
			'posts_per_page' => $count - count( $ids ),
			'post__not_in'   => array_merge( $ids, array( $post_id ) ),
			'orderby'        => 'rand',
			'fields'         => 'ids',
			'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery
				array(
					'taxonomy' => $taxonomy,
					'terms'    => $terms,
				),
			),
		) ) );
	}

	return array_map( 'intval', $ids );
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
