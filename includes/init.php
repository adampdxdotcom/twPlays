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
require_once TW_PLAYS_PATH . 'admin/core/editor-setup.php';


// =========================================================================
// == Play Pod Customizations
// =========================================================================
require_once TW_PLAYS_PATH . 'admin/list-tables/play-columns.php';


// =========================================================================
// == Actor Pod Customizations (NEWLY ADDED)
// =========================================================================
require_once TW_PLAYS_PATH . 'admin/list-tables/actor-columns.php';

