<?php
/**
 * Customizations for the 'Play' Pod Editor Screen. (v4 - CSS Brute Force)
 *
 * This version hides the block editor via CSS and ensures our custom meta box is primary.
 *
 * @package TW_Plays
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * 1. Add our own custom meta box to the 'play' editor screen.
 * We know this part is working correctly.
 */
function tw_plays_add_custom_meta_box_v4() {
    add_meta_box(
        'tw_plays_custom_fields_box',
        'Play Details',
        'tw_plays_render_custom_meta_box_v4',
        'play',
        'advanced',
        'high'
    );
}
add_action( 'add_meta_boxes', 'tw_plays_add_custom_meta_box_v4' );


/**
 * 2. Render the content of our custom meta box.
 */
function tw_plays_render_custom_meta_box_v4( $post ) {
    if ( ! function_exists( 'pods' ) ) { return; }
    $pod = pods( 'play', $post->ID );
    echo $pod->form();
}


/**
 * 3. Forcefully hide the Block Editor and original Pods box using inline CSS.
 * This is the most direct and reliable method to achieve the visual layout.
 */
function tw_plays_hide_editor_elements_css() {
    global $post_type;

    if ( 'play' === $post_type ) {
        echo '
        <style>
            /* Hide the main block editor writing area */
            .block-editor-writing-flow {
                display: none !important;
            }
            /* Hide the original Pods "More Fields" box to prevent duplicates */
            #pods-meta-more-fields { 
                display: none !important;
            }
        </style>
        ';
    }
}
add_action( 'admin_head-post.php', 'tw_plays_hide_editor_elements_css' );
add_action( 'admin_head-post-new.php', 'tw_plays_hide_editor_elements_css' );


/**
 * 4. Enqueue our stylesheet for the editor screen.
 * This function remains necessary for styling the fields inside our custom box.
 */
function tw_plays_enqueue_play_editor_assets_v4( $hook_suffix ) {
    global $post_type;

    if ( ( 'post-new.php' === $hook_suffix || 'post.php' === $hook_suffix ) && 'play' === $post_type ) {
        wp_enqueue_style(
            'tw-plays-play-editor-styles',
            TW_PLAYS_URL . 'admin/assets/css/admin-styles.css',
            [],
            '1.2.0' // Version bump
        );
    }
}
add_action( 'admin_enqueue_scripts', 'tw_plays_enqueue_play_editor_assets_v4' );
