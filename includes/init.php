<?php
/**
 * Plugin Initializer (Final Version with Actor Pod)
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

// =========================================================================
// == General Admin Pages
// =========================================================================
require_once TW_PLAYS_PATH . 'admin/menu.php';
require_once TW_PLAYS_PATH . 'admin/dashboard-page.php';


// =========================================================================
// == Play Pod Customizations
// =========================================================================
require_once TW_PLAYS_PATH . 'admin/list-tables/play-columns.php';
require_once TW_PLAYS_PATH . 'admin/list-tables/play-editor.php';


// =========================================================================
// == Actor Pod Customizations (NEWLY ADDED)
// =========================================================================
require_once TW_PLAYS_PATH . 'admin/list-tables/actor-columns.php';
require_once TW_PLAYS_PATH . 'admin/list-tables/actor-editor.php';
