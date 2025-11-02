<?php
/**
 * Plugin Name:       TW Plays
 * Plugin URI:        https://theatrewest.org/
 * Description:       A custom user interface for managing Theatre West's Pods data (Plays, Actors, Crew, etc.).
 * Version:           1.0.0
 * Requires at least: 5.8
 * Requires PHP:      7.4
 * Author:            Adam Michaels
 * Author URI:        https://github.com/your-username/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       tw-plays
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Define constants for easy access to paths and URLs.
 *
 * TW_PLAYS_PATH: The absolute server path to the plugin's root directory.
 * TW_PLAYS_URL: The public URL to the plugin's root directory.
 */
define( 'TW_PLAYS_PATH', plugin_dir_path( __FILE__ ) );
define( 'TW_PLAYS_URL', plugin_dir_url( __FILE__ ) );


/**
 * The main plugin bootstrap file.
 *
 * This file is responsible for including the main initializer,
 * which in turn loads all other necessary plugin files.
 */
require_once TW_PLAYS_PATH . 'includes/init.php';
