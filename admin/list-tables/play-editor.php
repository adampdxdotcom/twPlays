<?php
/**
 * Customizations for the 'Play' Pod Editor Screen. (v2 - More Robust)
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
 * This runs on 'init' and should work reliably.
 */
function tw_plays_remove_editor_support_for_play() {
    remove_post_type_support( 'play', 'editor' );
}
add_action( 'init', 'tw_plays_remove_editor_support_for_play' );


/**
 * 2. REVISED: Move the Pods "More Fields" meta box.
 *
 * This new version uses a different, more direct hook ('edit_form_after_title')
 * to physically output the Pods meta box right after the title field. It's
 * generally more reliable than trying to manipulate the global meta box array.
 */
function tw_plays_render_pods_box_after_title( $post ) {
    // We only want this to run on our 'play' post type.
    if ( 'play' !== $post->post_type ) {
        return;
    }
    
    // Manually render the Pods meta box in this new location.
    // 'pods-meta-more-fields' is the ID, 'play' is the screen, 'normal' is the context.
    do_meta_boxes( get_current_screen(), 'normal', $post );
    
    // We also need to hide the original meta box so it doesn't appear twice.
    ?>
    <style>
        #pods-meta-more-fields {
            display: none;
        }
    </style>
    <?php
}
add_action( 'edit_form_after_title', 'tw_plays_render_pods_box_after_title' );

/**
 * 3. Enqueue a specific stylesheet for the 'play' editor screen.
 * This part remains the same.
 */
function tw_plays_enqueue_play_editor_assets( $hook_suffix ) {
    global $post_type;

    if ( ( 'post-new.php' === $hook_suffix || 'post.php' === $hook_suffix ) && 'play' === $post_type ) {
        wp_enqueue_style(
            'tw-plays-play-editor-styles',
            TW_PLAYS_URL . 'admin/assets/css/admin-styles.css',
            [],
            '1.1.0'
        );
    }
}
add_action( 'admin_enqueue_scripts', 'tw_plays_enqueue_play_editor_assets' );
