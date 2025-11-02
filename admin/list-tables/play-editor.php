<?php
/**
 * Customizations for the 'Play' Pod Editor Screen.
 *
 * This file removes the default block editor and repositions the Pods custom
 * fields meta box for a cleaner, form-like data entry experience.
 *
 * @package TW_Plays
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * 1. Remove the main block editor for the 'play' post type.
 */
function tw_plays_remove_editor_support_for_play() {
    // We only want to run this once.
    if ( ! post_type_supports( 'play', 'editor' ) ) {
        return;
    }
    remove_post_type_support( 'play', 'editor' );
}
add_action( 'init', 'tw_plays_remove_editor_support_for_play' );


/**
 * 2. Move the Pods "More Fields" meta box to a high-priority position.
 */
function tw_plays_move_pods_meta_box() {
    // This hook runs after all meta boxes have been registered.
    add_action( 'add_meta_boxes', function() {
		die('The add_meta_boxes action IS running!');
        global $wp_meta_boxes;

        // The ID for the Pods meta box.
        $pods_box_id = 'pods-meta-more-fields';
        $post_type = 'play';

        // Check if the Pods meta box exists for this post type.
        if ( isset( $wp_meta_boxes[ $post_type ]['normal']['core'][ $pods_box_id ] ) ) {

            // Grab the entire meta box object.
            $pods_box = $wp_meta_boxes[ $post_type ]['normal']['core'][ $pods_box_id ];
            
            // Unset it from its original, low-priority position.
            unset( $wp_meta_boxes[ $post_type ]['normal']['core'][ $pods_box_id ] );
            
            // Add it back in the 'advanced' context with 'high' priority.
            // This moves it directly below the title.
            $wp_meta_boxes[ $post_type ]['advanced']['high'][ $pods_box_id ] = $pods_box;
        }
    }, 100 ); // Run with a late priority to ensure Pods has already added its box.
}
// Run our function only when on an admin screen.
add_action( 'admin_init', 'tw_plays_move_pods_meta_box' );


/**
 * 3. Enqueue a specific stylesheet for the 'play' editor screen.
 */
function tw_plays_enqueue_play_editor_assets( $hook_suffix ) {
    global $post_type;

    // Only load our assets on the 'Add New' or 'Edit' screens for the 'play' post type.
    if ( ( 'post-new.php' === $hook_suffix || 'post.php' === $hook_suffix ) && 'play' === $post_type ) {
        
        // We can reuse the main admin stylesheet for consistency.
        wp_enqueue_style(
            'tw-plays-play-editor-styles',
            TW_PLAYS_URL . 'admin/assets/css/admin-styles.css',
            [],
            '1.1.0'
        );
    }
}
add_action( 'admin_enqueue_scripts', 'tw_plays_enqueue_play_editor_assets' );
