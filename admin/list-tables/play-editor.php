<?php
/**
 * Customizations for the 'Play' Pod Editor Screen.
 *
 * @package TW_Plays
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 1. Modify post type support for 'play'.
function tw_plays_modify_play_editor_support_final() {
    // We no longer remove 'editor' support, allowing the Block Editor to load.
    // This makes the 'play' CPT consistent with the others.
    add_post_type_support( 'play', 'title' );
}
add_action( 'init', 'tw_plays_modify_play_editor_support_final' );

// 2. Add our custom meta box.
function tw_plays_add_custom_meta_box_final() {
    add_meta_box( 'tw_plays_custom_fields_box', 'Play Details', 'tw_plays_render_custom_meta_box_final', 'play', 'advanced', 'high' );
}
add_action( 'add_meta_boxes', 'tw_plays_add_custom_meta_box_final' );

/**
 * 3. Render the content of our custom meta box.
 *    REMOVED: The hidden #title field is no longer needed.
 */
function tw_plays_render_custom_meta_box_final( $post ) {
    // We no longer need the hidden title input. Just render the Pods form.
    // The native Block Editor title field will be used instead.
    if ( function_exists( 'pods' ) ) {
        $pod = pods( 'play', $post->ID );
        echo $pod->form();
    }
}

// 4. Hide original elements using CSS.
function tw_plays_hide_original_elements_css_final() {
    global $post_type;
    if ( 'play' === $post_type ) {
        // We now only hide the main block writing area ("Type / to choose a block"),
        // as the native title field is now visible and desired.
        echo '<style>.block-editor-writing-flow { display: none !important; }</style>';
    }
}
add_action( 'admin_head-post.php', 'tw_plays_hide_original_elements_css_final' );
add_action( 'admin_head-post-new.php', 'tw_plays_hide_original_elements_css_final' );

// 5. Enqueue assets.
function tw_plays_enqueue_play_editor_assets_final( $hook_suffix ) {
    global $post_type;
    // This list can remain as is, it correctly targets all relevant post types.
    $pod_slugs = [ 'play', 'actor', 'crew', 'casting_record', 'board_term', 'positions' ];
    if ( ( 'post-new.php' === $hook_suffix || 'post.php' === $hook_suffix ) && in_array( $post_type, $pod_slugs ) ) {
        wp_enqueue_style( 'tw-plays-admin-styles', TW_PLAYS_URL . 'admin/assets/css/admin-styles.css', [], '1.5.0' );
        wp_enqueue_script( 'tw-plays-editor-scripts-js', TW_PLAYS_URL . 'admin/assets/js/editor-scripts.js', [ 'jquery' ], '1.5.0', true );
    }
}
add_action( 'admin_enqueue_scripts', 'tw_plays_enqueue_play_editor_assets_final' );
