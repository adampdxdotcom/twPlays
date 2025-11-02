<?php
/**
 * Customizations for the 'Play' Pod Editor Screen. (FINAL SIMPLIFIED VERSION)
 *
 * This version uses the native WordPress title field as the primary input and
 * relies on the default Pods meta box. Its main job is to inject CSS to
*  collapse the block editor's content area for a clean UI.
 *
 * @package TW_Plays
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 1. Ensure the Block Editor and its title field are enabled for the 'play' post type.
function tw_plays_modify_play_editor_support_final() {
    add_post_type_support( 'play', 'title' );
}
add_action( 'init', 'tw_plays_modify_play_editor_support_final' );


// 2. We have REMOVED the functions for creating a custom meta box. We will use the default Pods box.


// 3. Inject CSS to collapse the Block Editor's main content area.
function tw_plays_collapse_editor_area_css() {
    // We only run this on the specific post types we want to modify.
    $post_types_to_modify = [ 'play', 'actor', 'crew', 'casting_record', 'board_term', 'positions', 'location', 'event' ];
    $current_post_type = get_post_type();

    if ( in_array( $current_post_type, $post_types_to_modify ) ) {
        echo '<style>
            /* Hides the inner container where blocks would go */
            .block-editor-block-list__layout {
                display: none !important;
            }
            /* Reduces the padding of the main content area to bring the meta boxes up */
            .edit-post-visual-editor__content-area {
                padding-top: 20px !important;
            }
        </style>';
    }
}
add_action( 'admin_head-post.php', 'tw_plays_collapse_editor_area_css' );
add_action( 'admin_head-post-new.php', 'tw_plays_collapse_editor_area_css' );


// 4. Enqueue assets. (This can remain for other pods and general styling).
function tw_plays_enqueue_play_editor_assets_final( $hook_suffix ) {
    global $post_type;
    $pod_slugs = [ 'play', 'actor', 'crew', 'casting_record', 'board_term', 'positions', 'location', 'event' ];
    if ( ( 'post-new.php' === $hook_suffix || 'post.php' === $hook_suffix ) && in_array( $post_type, $pod_slugs ) ) {
        wp_enqueue_style( 'tw-plays-admin-styles', TW_PLAYS_URL . 'admin/assets/css/admin-styles.css', [], '1.6.0' );
        wp_enqueue_script( 'tw-plays-editor-scripts-js', TW_PLAYS_URL . 'admin/assets/js/editor-scripts.js', [ 'jquery' ], '1.6.0', true );
    }
}
add_action( 'admin_enqueue_scripts', 'tw_plays_enqueue_play_editor_assets_final' );
