<?php
/**
 * Plugin Initializer (DEBUGGING - Step 1)
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

// Load the menu file (we know this works)
require_once TW_PLAYS_PATH . 'admin/menu.php';

// ADDING THIS FILE BACK
require_once TW_PLAYS_PATH . 'admin/dashboard-page.php';
