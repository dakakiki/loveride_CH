<?php
/**
 * Anreise & Parken (Geteilte Inhalte → Anreise & Parken): GeoJSON uploads, map data for layout_anreise.
 *
 * @package loveride
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'upload_mimes', function ( $mimes ) {

	$mimes['geojson'] = 'application/geo+json';

	return $mimes;
} );

add_filter( 'wp_check_filetype_and_ext', function ( $data, $file, $filename ) {

	if ( strtolower( pathinfo( (string) $filename, PATHINFO_EXTENSION ) ) === 'geojson' && is_array( json_decode( (string) file_get_contents( $file ), true ) ) ) {
		$data['ext']  = 'geojson';
		$data['type'] = 'application/geo+json';
	}

	return $data;
}, 10, 3 );

function lr_parse_position( $value ): ?array {

	if ( ! preg_match( '/^\s*(-?\d+(?:[.,]\d+)?)\s*[,;\s]\s*(-?\d+(?:[.,]\d+)?)\s*$/', (string) $value, $m ) ) {
		return null;
	}

	return array( (float) str_replace( ',', '.', $m[1] ), (float) str_replace( ',', '.', $m[2] ) );
}

function lr_anreise_geojson( $file ): ?array {

	$path = ! empty( $file['ID'] ) ? get_attached_file( (int) $file['ID'] ) : '';

	if ( ! $path || ! is_readable( $path ) || filesize( $path ) > 1024 * 1024 ) {
		return null;
	}

	$data = json_decode( (string) file_get_contents( $path ), true );

	return is_array( $data ) && ! empty( $data['type'] ) ? $data : null;
}

function lr_anreise_karte( array $karte ): array {

	$ebenen = array();

	foreach ( (array) ( ( $karte['ebenen'] ?? null ) ?: array() ) as $ebene ) {
		$name = trim( (string) ( $ebene['name'] ?? '' ) );

		if ( $name === '' ) {
			continue;
		}

		$marker = array();

		foreach ( (array) ( ( $ebene['marker'] ?? null ) ?: array() ) as $punkt ) {
			$pos = lr_parse_position( $punkt['position'] ?? '' );

			if ( $pos ) {
				$marker[] = array(
					'titel' => trim( (string) ( $punkt['titel'] ?? '' ) ) ?: $name,
					'text'  => trim( (string) ( $punkt['text'] ?? '' ) ),
					'pos'   => $pos,
				);
			}
		}

		$ebenen[] = array(
			'name'    => $name,
			'icon'    => (string) ( $ebene['icon'] ?? '' ),
			'hinweis' => trim( (string) ( $ebene['hinweis'] ?? '' ) ),
			'aktiv'   => ! empty( $ebene['standard_aktiv'] ),
			'marker'  => $marker,
			'geojson' => lr_anreise_geojson( $ebene['geojson'] ?? null ),
		);
	}

	return array(
		'ziel'   => trim( (string) ( $karte['ziel'] ?? '' ) ),
		'pos'    => lr_parse_position( $karte['position'] ?? '' ) ?: array( 47.3985, 8.6432 ),
		'zoom'   => (int) ( ( $karte['zoom'] ?? 0 ) ?: 15 ),
		'ebenen' => $ebenen,
	);
}
