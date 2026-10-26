<?php
/**
 * Debug: dd_array( $data, $text_color, $bg_color, $die ) – formatted print_r for development.
 *
 * @package loveride
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'dd_array' ) ) {

	function dd_array( $input, string $text_color = 'black', string $bg_color = 'white', bool $die = false ): void {

		if ( ! is_array( $input ) && ! is_object( $input ) ) {
			return;
		}

		echo '<pre style="color:' . esc_attr( $text_color ) . ';background-color:' . esc_attr( $bg_color ) . ';padding:15px;border:1px solid #ccc;font-size:14px;line-height:1.5;overflow:auto;">';
		echo esc_html( print_r( $input, true ) );
		echo '</pre>';

		if ( $die ) {
			die();
		}
	}
}
