<?php
/**
 * Admin: hides the unused core "Beiträge" (News uses its own post type), Seiten-Layouts row titles and collapsed repeater rows.
 *
 * @package loveride
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_head', function () {

	echo '<style>
		.acf-flexible-content .layout .acf-fc-layout-handle .lr-layout-meta {
			color: #757575;
			font-weight: 400;
		}
		.acf-repeater .acf-row.-collapsed .acf-field-group.-collapsed-target {
			width: 100% !important;
		}
		.acf-repeater .acf-row.-collapsed .acf-field-group.-collapsed-target > .acf-label,
		.acf-repeater .acf-row.-collapsed .acf-field-group.-collapsed-target .acf-field:not([data-name="titel"]) {
			display: none;
		}
		.acf-repeater .acf-row.-collapsed .acf-field-group.-collapsed-target > .acf-input > .acf-fields,
		.acf-repeater .acf-row.-collapsed .acf-field-group.-collapsed-target .acf-field[data-name="titel"] {
			border: 0;
		}
	</style>';
} );

add_action( 'admin_menu', function () {
	remove_menu_page( 'edit.php' );
} );

add_action( 'admin_bar_menu', function ( $wp_admin_bar ) {
	$wp_admin_bar->remove_node( 'new-post' );
}, 999 );
