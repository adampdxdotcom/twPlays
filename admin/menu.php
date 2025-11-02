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
    // 1. Create the main, top-level "TW Plays" menu item.
    add_menu_page(
        'TW Plays Dashboard',         // Page Title
        'TW Plays',                   // Menu Title
        'edit_posts',                 // Capability required to see it
        'tw-plays-dashboard',         // Menu Slug (for our main dashboard page)
        'tw_plays_render_dashboard_page', // Function to render the dashboard content
        'dashicons-tickets-alt',      // Icon (a ticket stub)
        25                            // Position (just below Comments)
    );

    // 2. Define our Pods and their desired submenu labels.
    $pods_to_add = [
        'play'             => 'All Plays',
        'actor'            => 'All Actors',
        'crew'             => 'All Crew',
        'casting_record'   => 'Casting Records',
        'board_term'       => 'Board Terms',
        'positions'        => 'Positions',
    ];

    // 3. Loop through the Pods and add them as submenus.
    foreach ( $pods_to_add as $pod_slug => $menu_label ) {
        add_submenu_page(
            'tw-plays-dashboard',      // Parent Slug (our main menu)
            $menu_label,               // Page Title
            $menu_label,               // Menu Title
            'edit_posts',              // Capability
            'edit.php?post_type=' . $pod_slug // The magic part: link to the existing Pods admin page.
        );
    }
}
add_action( 'admin_menu', 'tw_plays_create_admin_menu' );


/**
 * Removes the original, top-level Pods menus to avoid clutter.
 * We run this at a later priority (99) to ensure the menus exist before we try to remove them.
 */
function tw_plays_remove_default_pods_menus() {
    // Define the slugs of the top-level menus created by Pods that we want to remove.
    $pods_to_remove = [
        'play',
        'actor',
        'crew',
        'casting_record',
        'board_term',
        'positions',
    ];

    foreach ( $pods_to_remove as $pod_slug ) {
        remove_menu_page( 'edit.php?post_type=' . $pod_slug );
    }
}
add_action( 'admin_menu', 'tw_plays_remove_default_pods_menus', 99 );


/**
 * Enqueues the custom CSS for our admin pages.
 */
function tw_plays_enqueue_admin_assets( $hook_suffix ) {
    // Get the hook for our main dashboard page.
    $dashboard_hook = 'toplevel_page_tw-plays-dashboard';

    // Only load our custom stylesheet on our dashboard page.
    if ( $hook_suffix !== $dashboard_hook ) {
        return;
    }

    wp_enqueue_style(
        'tw-plays-admin-styles',
        TW_PLAYS_URL . 'admin/assets/css/admin-styles.css',
        [], // No dependencies
        '1.0.0'
    );

    // We can add our admin-scripts.js here in the future if needed.
}
add_action( 'admin_enqueue_scripts', 'tw_plays_enqueue_admin_assets' );
