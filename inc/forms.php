<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'wpcf7_autop_or_not', '__return_false' );
