<?php
/**
 * Customizations for the 'Play' Pod Editor Screen. (v9 - Unified Final)
 *
 * This version creates a custom meta box UI and relies on a 'play_name'
 * Pods field, which is then synced to the hidden native 'post_title' field via JS.
 *
 * @package TW_Plays
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * 1. Modify post type support for 'play'.
 * We remove the block editor but explicitly keep 'title' support so the hidden field is available.
 */
function tw_plays_modify_play_editor_support_v9() {
    remove_post_type_support( 'play', 'editor' );
    add_post_type_support( 'play', 'title' );
}
add_action( 'init', 'tw_plays_modify_play_editor_support_v9' );

/**
 * 2. Add our custom meta box to the 'play' editor screen.
 */
function tw_plays_add_custom_meta_box_v9() {
    add_meta_box( 'tw_plays_custom_fields_box', 'Play Details', 'tw_plays_render_custom_meta_box_v9', 'play', 'advanced', 'high' );
}
add_action( 'add_meta_boxes', 'tw_plays_add_custom_meta_box_v9' );

/**
 * 3. Render the content of our custom meta box using the Pods form function.
 */
function tw_plays_render_custom_meta_box_v9( $post ) {
    if ( ! function_exists( 'pods' ) ) { return; }
    $pod = pods( 'play', $post->ID );
    echo $pod->form();
}

/**
 * 4. Hide all the original, now-redundant UI elements using CSS.
 */
function tw_plays_hide_original_elements_css_v9() {
    global $post_type;
    if ( 'play' === $post_type ) {
        echo '
        <style>
            /* Hide the original WordPress title wrapper */
            .edit-post-visual-editor__post-title-wrapper { display: none !important; }
            /* Hide the block editor (belt-and-suspenders) */
            .block-editor-writing-flow { display: none !important; }
            /* Hide the original Pods meta box */
            #pods-meta-more-fields { display: none !important; }
        </style>';
    }
}
add_action( 'admin_head-post.php', 'tw_plays_hide_original_elements_css_v9' );
add_action( 'admin_head-post-new.php', 'tw_plays_hide_original_elements_css_v9' );

/**
 * 5. Enqueue our stylesheet for the editor screen.
 */
function tw_plays_enqueue_play_editor_assets_v9( $hook_suffix ) {
    global $post_type;
    if ( ( 'post-new.php' === $hook_suffix || 'post.php' === $hook_suffix ) && 'play' === $post_type ) {
        wp_enqueue_style( 'tw-plays-play-editor-styles', TW_PLAYS_URL . 'admin/assets/css/admin-styles.css', [], '1.4.0' );
    }
}
add_action( 'admin_enqueue_scripts', 'tw_plays_enqueue_play_editor_assets_v9' );
