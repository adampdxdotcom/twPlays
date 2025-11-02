<?php
/**
 * Plugin Initializer (DEBUGGING MODE)
 *
 * @package TW_Plays
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Include the core plugin components.
 */

// Loads the admin menu and handles menu modifications.
require_once TW_PLAYS_PATH . 'admin/menu.php';

// Loads the custom dashboard page content.
require_once TW_PLAYS_PATH . 'admin/dashboard-page.php';

// Loads the custom columns and AJAX functionality for the Play list table.
require_once TW_PLAYS_PATH . 'admin/list-tables/play-columns.php';

// --- WE ARE COMMENTING OUT THE PLAY EDITOR FOR THIS TEST ---
// require_once TW_PLAYS_PATH . 'admin/list-tables/play-editor.php';

// --- WE ARE LOADING ONLY THE ACTOR TEST FILE ---
require_once TW_PLAYS_PATH . 'admin/list-tables/actor-editor-test.php';
