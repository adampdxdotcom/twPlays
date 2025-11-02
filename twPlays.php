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
 */
define( 'TW_PLAYS_PATH', plugin_dir_path( __FILE__ ) );
define( 'TW_PLAYS_URL', plugin_dir_url( __FILE__ ) );


/**
 * The main setup function for the plugin.
 *
 * This function is hooked into 'plugins_loaded', which ensures that all other plugins
 * (like Pods) are loaded and ready before our plugin starts adding its hooks.
 */
function tw_plays_initialize_plugin() {
    // Now that we're in a safe loading spot, we can include our initializer file.
    require_once TW_PLAYS_PATH . 'includes/init.php';
}
// This is the key: we run our setup function at the correct time.
add_action( 'plugins_loaded', 'tw_plays_initialize_plugin' );
