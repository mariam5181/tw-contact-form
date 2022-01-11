<?php
/*
Plugin Name:  10Web Contact Form
Description:  Simple contact form
Version:      1.1.0
Author:       Mariam Mkrtchyan
Text Domain:  tw
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'TW_MAIN_FILE' ) ) {
	define( 'TW_MAIN_FILE', __FILE__ );
}

require_once __DIR__ . '/admin/table.php';
require_once __DIR__ . '/hooks.php';
require_once __DIR__ . '/functions.php';
