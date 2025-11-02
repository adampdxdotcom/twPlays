<?php
/**
 * Admin Menu Setup (BARE MINIMUM TEST MODE)
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds a highly visible message to the footer of every admin page.
 * We are using the admin_footer hook because we know it works.
 */
function tw_plays_bare_minimum_test() {
    echo '<div style="position: fixed; bottom: 0; left: 0; width: 100%; padding: 15px; background-color: #007cba; color: #ffffff; font-size: 20px; font-weight: bold; text-align: center; z-index: 99999;">';
    echo 'TW PLAYS PLUGIN IS ALIVE: The core loading mechanism is working!';
    echo '</div>';
}
add_action( 'admin_footer', 'tw_plays_bare_minimum_test' );
