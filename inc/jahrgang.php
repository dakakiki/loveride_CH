<?php
/**
 * Jahrgänge (Rückblick): Jahrgang posts sorted by year (newest first), their Kennzahlen and the helper list sorted by last name.
 *
 * @package loveride
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @return WP_Post[] newest year first
 */
function lr_jahrgaenge(): array {

	$posts = get_posts( array(
		'post_type'      => 'jahrgang',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'no_found_rows'  => true,
	) );

	usort( $posts, fn( $a, $b ) => lr_jahrgang_jahr( $b ) <=> lr_jahrgang_jahr( $a ) );

	return $posts;
}

function lr_jahrgang_jahr( WP_Post $post ): int {

	$jahr  = (int) get_field( 'jahr', $post->ID );
	$datum = (string) get_field( 'datum', $post->ID );

	return $jahr ?: ( preg_match( '/^\d{4}/', $datum ) ? (int) substr( $datum, 0, 4 ) : (int) $post->post_title );
}

/**
 * @return array<int, array{label: string, wert: string}> Besuchende, Mitfahrende, Spenden, Ride-out („–“ when empty)
 */
function lr_jahrgang_kennzahlen( WP_Post $post ): array {

	$werte  = (array) ( get_field( 'kennzahlen', $post->ID ) ?: array() );
	$labels = array(
		'besuchende'  => __( 'Besuchende', 'loveride' ),
		'mitfahrende' => __( 'Mitfahrende', 'loveride' ),
		'spenden'     => __( 'Spenden', 'loveride' ),
		'ride_out'    => __( 'Ride-out', 'loveride' ),
	);

	return array_map( fn( $key ) => array(
		'label' => $labels[ $key ],
		'wert'  => trim( (string) ( $werte[ $key ] ?? '' ) ),
	), array_keys( $labels ) );
}

/**
 * @return string[] one name per line, sorted by last name, then first name
 */
function lr_jahrgang_helfer( WP_Post $post ): array {

	$namen = array_values( array_filter( array_map( 'trim', preg_split( '/\R/', (string) get_field( 'helferliste', $post->ID ) ) ) ) );
	$key   = function ( string $name ): string {
		$teile = preg_split( '/\s+/', $name );

		return remove_accents( mb_strtolower( array_pop( $teile ) . ' ' . implode( ' ', $teile ) ) );
	};

	usort( $namen, fn( $a, $b ) => strcmp( $key( $a ), $key( $b ) ) );

	return $namen;
}
