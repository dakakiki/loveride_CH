<?php
/**
 * Anreise & Parken (Geteilte Inhalte → Anreise & Parken): GeoJSON and GPX uploads (GPX read as GeoJSON), map data for layout_anreise and layout_text_karte, layer choices for layout_text_karte.
 *
 * @package loveride
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'upload_mimes', function ( $mimes ) {

	$mimes['geojson'] = 'application/geo+json';
	$mimes['gpx']     = 'application/gpx+xml';

	return $mimes;
} );

add_filter( 'wp_check_filetype_and_ext', function ( $data, $file, $filename ) {

	$ext = strtolower( pathinfo( (string) $filename, PATHINFO_EXTENSION ) );

	if ( $ext === 'geojson' && is_array( json_decode( (string) file_get_contents( $file ), true ) ) ) {
		$data['ext']  = 'geojson';
		$data['type'] = 'application/geo+json';
	}

	if ( $ext === 'gpx' && strpos( (string) file_get_contents( $file, false, null, 0, 2048 ), '<gpx' ) !== false ) {
		$data['ext']  = 'gpx';
		$data['type'] = 'application/gpx+xml';
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

	if ( ! $path || ! is_readable( $path ) || filesize( $path ) > 5 * 1024 * 1024 ) {
		return null;
	}

	if ( strtolower( pathinfo( $path, PATHINFO_EXTENSION ) ) === 'gpx' ) {
		return lr_gpx_geojson( $path );
	}

	$data = json_decode( (string) file_get_contents( $path ), true );

	return is_array( $data ) && ! empty( $data['type'] ) ? $data : null;
}

function lr_gpx_geojson( string $path ): ?array {

	$gpx = simplexml_load_string( (string) file_get_contents( $path ), 'SimpleXMLElement', LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING );

	if ( ! $gpx ) {
		return null;
	}

	$features = array();
	$lines    = array_merge( $gpx->xpath( '//*[local-name()="trkseg"]' ) ?: array(), $gpx->xpath( '//*[local-name()="rte"]' ) ?: array() );

	foreach ( $lines as $line ) {
		$points = array();

		foreach ( $line->xpath( './*[local-name()="trkpt" or local-name()="rtept"]' ) ?: array() as $point ) {
			$points[] = array( round( (float) $point['lon'], 6 ), round( (float) $point['lat'], 6 ) );
		}

		if ( count( $points ) > 1500 ) {
			$step   = (int) ceil( count( $points ) / 1500 );
			$last   = end( $points );
			$points = array_values( array_filter( $points, fn( $key ) => $key % $step === 0, ARRAY_FILTER_USE_KEY ) );
			$points[] = $last;
		}

		if ( count( $points ) > 1 ) {
			$features[] = array( 'type' => 'Feature', 'properties' => new stdClass(), 'geometry' => array( 'type' => 'LineString', 'coordinates' => $points ) );
		}
	}

	return $features ? array( 'type' => 'FeatureCollection', 'features' => $features ) : null;
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

add_filter( 'acf/prepare_field/key=field_bdc7c3ce588de', function ( $field ) {

	$ebenen = lr_anreise_karte( (array) ( lr_get_option( 'geteilt_anreise', array() )['karte'] ?? array() ) )['ebenen'];

	$field['choices'] = array_combine(
		array_map( fn( $ebene ) => sanitize_title( $ebene['name'] ), $ebenen ),
		wp_list_pluck( $ebenen, 'name' )
	) ?: array();

	return $field;
} );
