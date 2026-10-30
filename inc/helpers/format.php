<?php
/**
 * Formatting helpers per style guide (dates, times, CHF amounts, numbers, phone links, website domains), e.g. So, 2. Mai 2027 · 8.00 Uhr · CHF 30.– · 5'000 · moto.ch.
 *
 * @package loveride
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const LR_DAYS = array( 'Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag' );

const LR_DAYS_SHORT = array( 'So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa' );

const LR_MONTHS = array( 1 => 'Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember' );

function lr_to_datetime( $date ): ?DateTimeImmutable {

	if ( $date instanceof DateTimeInterface ) {
		return DateTimeImmutable::createFromInterface( $date );
	}

	if ( is_int( $date ) || ( is_string( $date ) && ctype_digit( $date ) && strlen( $date ) !== 8 ) ) {
		return ( new DateTimeImmutable( '@' . (int) $date ) )->setTimezone( wp_timezone() );
	}

	if ( ! is_string( $date ) || $date === '' ) {
		return null;
	}

	if ( preg_match( '/^\d{8}$/', $date ) ) {
		$result = DateTimeImmutable::createFromFormat( '!Ymd', $date, wp_timezone() );

		return $result ?: null;
	}

	try {
		return new DateTimeImmutable( $date, wp_timezone() );
	} catch ( Exception $e ) {
		return null;
	}
}

/**
 * @param string $style short "So, 2. Mai 2027" | long "Sonntag, 2. Mai 2027" | plain "2. Mai 2027" | upper "SO, 2. MAI"
 */
function lr_format_date( $date, string $style = 'short' ): string {

	$dt = lr_to_datetime( $date );

	if ( ! $dt ) {
		return '';
	}

	$weekday = (int) $dt->format( 'w' );
	$plain   = $dt->format( 'j' ) . '. ' . LR_MONTHS[ (int) $dt->format( 'n' ) ] . ' ' . $dt->format( 'Y' );

	switch ( $style ) {
		case 'long':
			return LR_DAYS[ $weekday ] . ', ' . $plain;
		case 'plain':
			return $plain;
		case 'upper':
			return mb_strtoupper( LR_DAYS_SHORT[ $weekday ] . ', ' . $dt->format( 'j' ) . '. ' . LR_MONTHS[ (int) $dt->format( 'n' ) ] );
		default:
			return LR_DAYS_SHORT[ $weekday ] . ', ' . $plain;
	}
}

function lr_format_time( $time, bool $suffix = true ): string {

	if ( empty( $time ) ) {
		return '';
	}

	$dt = lr_to_datetime( is_string( $time ) && preg_match( '/^\d{1,2}:\d{2}/', $time ) ? 'today ' . $time : $time );

	if ( ! $dt ) {
		return '';
	}

	return $dt->format( 'G.i' ) . ( $suffix ? ' Uhr' : '' );
}

function lr_format_time_range( $from, $to ): string {

	if ( empty( $to ) ) {
		return lr_format_time( $from );
	}

	return lr_format_time( $from, false ) . ' bis ' . lr_format_time( $to );
}

function lr_format_number( $number, int $decimals = 0 ): string {

	if ( $number === '' || $number === null || ! is_numeric( $number ) ) {
		return (string) $number;
	}

	return number_format( (float) $number, $decimals, ',', "'" );
}

function lr_format_chf( $amount, bool $dash = true ): string {

	if ( $amount === '' || $amount === null || ! is_numeric( $amount ) ) {
		return '';
	}

	$amount = (float) $amount;

	if ( floor( $amount ) !== $amount ) {
		return 'CHF ' . number_format( $amount, 2, '.', "'" );
	}

	return 'CHF ' . number_format( $amount, 0, '.', "'" ) . ( $dash ? '.–' : '' );
}

function lr_tel_href( string $phone, string $country_prefix = '+41' ): string {

	$digits = preg_replace( '/[^0-9\+]/', '', $phone );

	if ( $digits === '' ) {
		return '';
	}

	if ( strpos( $digits, '+' ) !== 0 && strpos( $digits, '0' ) === 0 ) {
		$digits = $country_prefix . substr( $digits, 1 );
	}

	return 'tel:' . $digits;
}

function lr_url_domain( string $url ): string {

	$host = (string) wp_parse_url( $url, PHP_URL_HOST );

	return $host !== '' ? preg_replace( '/^www\./i', '', $host ) : trim( $url );
}
