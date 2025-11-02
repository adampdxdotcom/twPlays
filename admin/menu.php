<?php
/**
 * Admin Menu Setup
 *
 * This file handles the creation of the custom "TW Plays" menu, its submenus,
 * and the removal of the default Pods menus for a cleaner interface.
 *
 * @package TW_Plays
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Creates the main "TW Plays" admin menu and its submenus.
 */
function tw_plays_create_admin_menu() {
    add_menu_page( 'TW Plays Dashboard', 'TW Plays', 'edit_posts', 'tw-plays-dashboard', 'tw_plays_render_dashboard_page', 'dashicons-tickets-alt', 25 );
    $pods_to_add = [
        'play'             => 'All Plays',
        'actor'            => 'All Actors',
        'crew'             => 'All Crew',
        'casting_record'   => 'Casting Records',
        'board_term'       => 'Board Terms',
        'positions'        => 'Positions',
    ];
    foreach ( $pods_to_add as $pod_slug => $menu_label ) {
        add_submenu_page( 'tw-plays-dashboard', $menu_label, $menu_label, 'edit_posts', 'edit.php?post_type=' . $pod_slug );
    }
}
add_action( 'admin_menu', 'tw_plays_create_admin_menu' );


/**
 * Removes the original, top-level Pods menus to avoid clutter.
 */
function tw_plays_remove_default_pods_menus() {
    $pods_to_remove = [ 'play', 'actor', 'crew', 'casting_record', 'board_term', 'positions' ];
    foreach ( $pods_to_remove as $pod_slug ) {
        remove_menu_page( 'edit.php?post_type=' . $pod_slug );
    }
}
add_action( 'admin_menu', 'tw_plays_remove_default_pods_menus', 99 );


/**
 * Enqueues the custom CSS and JS for our admin pages.
 */
function tw_plays_enqueue_admin_assets( $hook_suffix ) {
    global $pagenow;

    // --- 1. Load assets for the custom Dashboard page ---
    if ( 'toplevel_page_tw-plays-dashboard' === $hook_suffix ) {
        wp_enqueue_style( 'tw-plays-admin-styles', TW_PLAYS_URL . 'admin/assets/css/admin-styles.css', [], '1.1.0' );
    }
    
    // --- 2. Load assets for the "All Plays" list table page ---
    // We check for the page name (edit.php) and the post type (play).
    if ( 'edit.php' === $pagenow && isset( $_GET['post_type'] ) && 'play' === $_GET['post_type'] ) {
        
        // Enqueue our new JavaScript file for the interactive toggles.
        wp_enqueue_script(
            'tw-plays-list-tables-js',
            TW_PLAYS_URL . 'admin/assets/js/admin-list-tables.js',
            [ 'jquery' ], // This script uses jQuery.
            '1.1.0',
            true // Load in the footer.
        );

        // This is the crucial part that passes the security nonce to our JavaScript.
        wp_localize_script(
            'tw-plays-list-tables-js',      // The handle of the script to attach data to.
            'tw_plays_ajax',                // The name of the JavaScript object to create.
            [ 'nonce' => wp_create_nonce( 'tw_plays_ajax_nonce' ) ] // The data to pass.
        );
    }
}
add_action( 'admin_enqueue_scripts', 'tw_plays_enqueue_admin_assets' );
