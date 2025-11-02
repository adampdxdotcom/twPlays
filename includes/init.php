<?php
/**
 * Plugin Initializer (DEBUGGING - Step 2)
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

// Load the known good files
require_once TW_PLAYS_PATH . 'admin/menu.php';
require_once TW_PLAYS_PATH . 'admin/dashboard-page.php';

// ADDING THIS FILE BACK
require_once TW_PLAYS_PATH . 'admin/list-tables/play-columns.php';
