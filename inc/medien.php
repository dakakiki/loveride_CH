<?php
/**
 * Medien: Medienmitteilung posts sorted by their date (newest first) and their files per language for the download lists.
 *
 * @package loveride
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return WP_Post[] newest first (field „Datum“, otherwise publish date)
 */
function lr_medienmitteilungen(): array {

	$posts = get_posts( array(
		'post_type'      => 'medienmitteilung',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'no_found_rows'  => true,
	) );

	usort( $posts, fn( $a, $b ) => strcmp( lr_medienmitteilung_datum( $b ), lr_medienmitteilung_datum( $a ) ) );

	return $posts;
}

function lr_medienmitteilung_datum( WP_Post $post ): string {

	$datum = (string) get_field( 'datum', $post->ID );

	return preg_match( '/^\d{8}$/', $datum ) ? $datum : get_the_date( 'Ymd', $post );
}

/**
 * @return array<int, array{titel: string, url: string, typ: string, sprache: string}> files in the order entered
 */
function lr_medienmitteilung_dateien( WP_Post $post ): array {

	$sprachen = array(
		'de' => 'Deutsch',
		'fr' => 'Français',
		'it' => 'Italiano',
		'en' => 'English',
	);
	$dateien  = array();

	foreach ( (array) ( get_field( 'dateien', $post->ID ) ?: array() ) as $row ) {
		$datei = (array) ( $row['datei'] ?? array() );

		if ( empty( $datei['url'] ) ) {
			continue;
		}

		$sprache = (string) ( $row['sprache'] ?? '' );
		$titel   = trim( (string) ( $row['bezeichnung'] ?? '' ) ) ?: trim( __( 'Medienmitteilung', 'loveride' ) . ' ' . ( $sprachen[ $sprache ] ?? '' ) );

		$dateien[] = array(
			'titel'   => $titel,
			'url'     => (string) $datei['url'],
			'typ'     => strtoupper( (string) pathinfo( (string) ( $datei['filename'] ?? $datei['url'] ), PATHINFO_EXTENSION ) ),
			'sprache' => $sprache,
		);
	}

	return $dateien;
}
