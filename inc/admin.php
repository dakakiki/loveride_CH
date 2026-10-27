<?php
/**
 * Admin: hides the unused core "Beiträge" (News uses its own post type).
 *
 * @package loveride
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', function () {
	remove_menu_page( 'edit.php' );
} );

add_action( 'admin_bar_menu', function ( $wp_admin_bar ) {
	$wp_admin_bar->remove_node( 'new-post' );
}, 999 );
