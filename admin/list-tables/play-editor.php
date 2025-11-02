<?php
/**
 * Customizations for the 'Play' Pod Editor Screen. (SIMPLIFIED VERSION)
 *
 * This version removes the custom meta box and relies on the native Pods meta box.
 * It keeps the Block Editor enabled in a minimal state (title only) and enqueues
 * assets for title-syncing functionality.
 *
 * @package TW_Plays
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 1. Ensure the Block Editor is enabled for the 'play' post type.
function tw_plays_modify_play_editor_support_final() {
    // This ensures the modern editor loads, making it consistent with other post types.
    add_post_type_support( 'play', 'title' );
}
add_action( 'init', 'tw_plays_modify_play_editor_support_final' );


// 2. We have removed the 'add_meta_box' and its render callback function.
//    The native Pods meta box ("More Fields") will now be used instead.


// 3. Hide the main writing area of the Block Editor using CSS.
function tw_plays_hide_original_elements_css_final() {
    global $post_type;
    if ( 'play' === $post_type ) {
        // Hides the "Type / to choose a block" area, leaving a clean interface.
        echo '<style>.block-editor-writing-flow { display: none !important; }</style>';
    }
}
add_action( 'admin_head-post.php', 'tw_plays_hide_original_elements_css_final' );
add_action( 'admin_head-post-new.php', 'tw_plays_hide_original_elements_css_final' );

// 4. Enqueue assets for title-syncing and styling.
function tw_plays_enqueue_play_editor_assets_final( $hook_suffix ) {
    global $post_type;
    // This list correctly targets all relevant post types.
    $pod_slugs = [ 'play', 'actor', 'crew', 'casting_record', 'board_term', 'positions' ];
    if ( ( 'post-new.php' === $hook_suffix || 'post.php' === $hook_suffix ) && in_array( $post_type, $pod_slugs ) ) {
        wp_enqueue_style( 'tw-plays-admin-styles', TW_PLAYS_URL . 'admin/assets/css/admin-styles.css', [], '1.5.0' );
        wp_enqueue_script( 'tw-plays-editor-scripts-js', TW_PLAYS_URL . 'admin/assets/js/editor-scripts.js', [ 'jquery' ], '1.5.0', true );
    }
}
add_action( 'admin_enqueue_scripts', 'tw_plays_enqueue_play_editor_assets_final' );
