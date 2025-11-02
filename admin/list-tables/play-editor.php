<?php
/**
 * Customizations for the 'Play' Pod Editor Screen. (FINAL WORKING VERSION)
 *
 * This version creates a unified data entry experience by adding a hidden '#title'
 * input that is synced via JavaScript, allowing the "Publish" button to work.
 *
 * @package TW_Plays
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 1. Modify post type support for 'play'.
function tw_plays_modify_play_editor_support_final() {
    remove_post_type_support( 'play', 'editor' );
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
 *    CRUCIAL ADDITION: We manually add our own hidden #title field.
 */
function tw_plays_render_custom_meta_box_final( $post ) {
    // Manually create a hidden input with the ID and name 'title'.
    // This gives the "Publish" button's JavaScript the target it's looking for.
    // Our own JS will keep this field's value in sync with the visible 'play_name' field.
    echo '<input type="hidden" id="title" name="title" value="' . esc_attr( $post->post_title ) . '">';

    // Now, render the Pods form fields below.
    if ( function_exists( 'pods' ) ) {
        $pod = pods( 'play', $post->ID );
        echo $pod->form();
    }
}

// 4. Hide original elements using CSS.
function tw_plays_hide_original_elements_css_final() {
    global $post_type;
    if ( 'play' === $post_type ) {
        echo '<style>.edit-post-visual-editor__post-title-wrapper, .block-editor-writing-flow, #pods-meta-more-fields { display: none !important; }</style>';
    }
}
add_action( 'admin_head-post.php', 'tw_plays_hide_original_elements_css_final' );
add_action( 'admin_head-post-new.php', 'tw_plays_hide_original_elements_css_final' );

// 5. Enqueue assets.
function tw_plays_enqueue_play_editor_assets_final( $hook_suffix ) {
    global $post_type;
    $pod_slugs = [ 'play', 'actor', 'crew', 'casting_record', 'board_term', 'positions' ]; // List of all relevant pods
    if ( ( 'post-new.php' === $hook_suffix || 'post.php' === $hook_suffix ) && in_array( $post_type, $pod_slugs ) ) {
        wp_enqueue_style( 'tw-plays-admin-styles', TW_PLAYS_URL . 'admin/assets/css/admin-styles.css', [], '1.5.0' );
        wp_enqueue_script( 'tw-plays-editor-scripts-js', TW_PLAYS_URL . 'admin/assets/js/editor-scripts.js', [ 'jquery' ], '1.5.0', true );
    }
}
add_action( 'admin_enqueue_scripts', 'tw_plays_enqueue_play_editor_assets_final' );
