<?php
/**
 * Customizations for the 'Play' Pod Editor Screen. (v3 - Custom Meta Box)
 *
 * This version uses a custom meta box to display Pods fields, bypassing
 * potential conflicts with the default editor screen hooks.
 *
 * @package TW_Plays
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * 1. Remove the default Block Editor for the 'play' post type.
 */
function tw_plays_remove_editor_support_for_play_v3() {
    remove_post_type_support( 'play', 'editor' );
}
add_action( 'init', 'tw_plays_remove_editor_support_for_play_v3' );


/**
 * 2. Add our own custom meta box to the 'play' editor screen.
 */
function tw_plays_add_custom_meta_box() {
    add_meta_box(
        'tw_plays_custom_fields_box',       // Unique ID for our meta box
        'Play Details',                     // Title of the meta box
        'tw_plays_render_custom_meta_box',  // The function that will render the content
        'play',                             // The post type to add it to
        'advanced',                         // The context (main column)
        'high'                              // The priority (at the top)
    );
}
add_action( 'add_meta_boxes', 'tw_plays_add_custom_meta_box' );


/**
 * 3. Render the content of our custom meta box.
 * This function will use the Pods API to display the fields.
 */
function tw_plays_render_custom_meta_box( $post ) {
    // Check if the Pods function exists to prevent fatal errors.
    if ( ! function_exists( 'pods' ) ) {
        echo 'Pods plugin is not active.';
        return;
    }

    // Get the Pods object for the current post.
    $pod = pods( 'play', $post->ID );

    // This is the magic function that renders the form fields for all items in the Pod.
    echo $pod->form();
}


/**
 * 4. Hide the original Pods "More Fields" meta box using CSS.
 * This is a simple and reliable way to prevent duplicate fields from showing.
 */
function tw_plays_hide_original_pods_box_css() {
    global $post_type;

    // Only run this on the 'play' editor screen.
    if ( 'play' === $post_type ) {
        echo '<style>#pods-meta-more-fields { display: none !important; }</style>';
    }
}
// 'admin_head' is a reliable hook for adding CSS to the <head> of an admin page.
add_action( 'admin_head-post.php', 'tw_plays_hide_original_pods_box_css' );
add_action( 'admin_head-post-new.php', 'tw_plays_hide_original_pods_box_css' );
