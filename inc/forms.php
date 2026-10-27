<?php
/**
 * Forms: Contact Form 7 setup (markup without automatic paragraphs).
 *
 * @package loveride
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'wpcf7_autop_or_not', '__return_false' );
