<?php
/**
 * Plugin Initializer (BARE MINIMUM TEST MODE)
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// We are only loading menu.php for this test.
require_once TW_PLAYS_PATH . 'admin/menu.php';
