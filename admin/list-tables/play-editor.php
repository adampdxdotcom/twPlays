<?php
/**
 * Customizations for the 'Play' Pod Editor Screen. (FINAL - WHITESPACE COLLAPSE)
 *
 * This file's only job is to inject targeted CSS into the admin head.
 * This CSS overrides the Block Editor's default minimum height, effectively
 * collapsing the empty content area and removing the large white space.
 *
 * @package TW_Plays
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 1. Ensure the Block Editor and its title field are enabled.
function tw_plays_modify_play_editor_support_final() {
    add_post_type_support( 'play', 'title' );
}
add_action( 'init', 'tw_plays_modify_play_editor_support_final' );


// 2. Inject the CSS to collapse the editor's main content area.
function tw_plays_collapse_editor_area_css() {
    // A list of all post types that should have the collapsed editor UI.
    $post_types_to_modify = [ 'play', 'actor', 'crew', 'casting_record', 'board_term', 'positions', 'location', 'event' ];
    $current_screen = get_current_screen();

    // Check if we are on an edit screen for one of the specified post types.
    if ( $current_screen && in_array( $current_screen->post_type, $post_types_to_modify ) ) {
        echo '
        <style>
            /*
             * THE KEY FIX:
             * This targets the inner content area and overrides its default
             * minimum height, allowing it to shrink down to nothing.
             * This makes the container "smaller" without hiding it.
            */
            .block-editor-block-list__layout {
                min-height: 0 !important;
            }

            /*
             * This reduces the padding of the parent container,
             * bringing the meta boxes up closer to the title for a clean look.
            */
            .edit-post-visual-editor__content-area {
                padding-top: 20px !important;
                padding-bottom: 0 !important;
            }
        </style>';
    }
}
// Hook into the admin head for both new and existing post edit screens.
add_action( 'admin_head-post.php', 'tw_plays_collapse_editor_area_css' );
add_action( 'admin_head-post-new.php', 'tw_plays_collapse_editor_area_css' );


// 3. Enqueue other assets. This is still useful for your JS and other CSS.
function tw_plays_enqueue_play_editor_assets_final( $hook_suffix ) {
    global $post_type;
    $pod_slugs = [ 'play', 'actor', 'crew', 'casting_record', 'board_term', 'positions', 'location', 'event' ];
    if ( ( 'post-new.php' === $hook_suffix || 'post.php' === $hook_suffix ) && in_array( $post_type, $pod_slugs ) ) {
        wp_enqueue_style( 'tw-plays-admin-styles', TW_PLAYS_URL . 'admin/assets/css/admin-styles.css', [], '1.8.0' );
        wp_enqueue_script( 'tw-plays-editor-scripts-js', TW_PLAYS_URL . 'admin/assets/js/editor-scripts.js', [ 'jquery' ], '1.8.0', true );
    }
}
add_action( 'admin_enqueue_scripts', 'tw_plays_enqueue_play_editor_assets_final' );
