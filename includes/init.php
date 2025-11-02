<?php
/**
 * Plugin Initializer
 *
 * This file is responsible for loading all the necessary PHP files (components)
 * that make up the plugin's functionality.
 *
 * @package TW_Plays
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Include the core plugin components.
 *
 * We use the TW_PLAYS_PATH constant defined in the main plugin file
 * to ensure our paths are always correct.
 */

// Loads the admin menu and handles menu modifications.
require_once TW_PLAYS_PATH . 'admin/menu.php';

// Loads the custom dashboard page content.
require_once TW_PLAYS_PATH . 'admin/dashboard-page.php';

// Loads the custom columns and AJAX functionality for the Play list table.
require_once TW_PLAYS_PATH . 'admin/list-tables/play-columns.php';

// --- NEW: Loads the customizations for the Play editor screen. ---
require_once TW_PLAYS_PATH . 'admin/list-tables/play-editor.php';
