<?php
/**
 * Customizations for the 'Play' Pod Editor Screen. (FINAL & ROBUST VERSION)
 *
 * This version uses the modern Block Editor but creates its own custom meta box.
 * This explicit approach guarantees that Pods loads the correct data for the post
 * being edited, solving the "blank fields on edit" issue.
 *
 * @package TW_Plays
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 1. Ensure the Block Editor is enabled for the 'play' post type.
function tw_plays_modify_play_editor_support_final() {
    add_post_type_support( 'play', 'title' );
}
add_action( 'init', 'tw_plays_modify_play_editor_support_final' );

// 2. Add our own custom meta box again. This gives us control over data loading.
function tw_plays_add_custom_meta_box_final() {
    add_meta_box(
        'tw_plays_custom_fields_box',          // Unique ID
        'Play Details',                        // Box title
        'tw_plays_render_custom_meta_box_final', // Content callback function
        'play',                                // Post type
        'advanced',                            // Context
        'high'                                 // Priority
    );
}
add_action( 'add_meta_boxes', 'tw_plays_add_custom_meta_box_final' );

/**
 * 3. Render the content of our custom meta box.
 *    CRUCIAL: We explicitly initialize Pods with the current post's ID.
 *    This guarantees the form fields will be populated with saved data.
 */
function tw_plays_render_custom_meta_box_final( $post ) {
    if ( function_exists( 'pods' ) ) {
        // Initialize the Pods object for the 'play' pod using the current post ID.
        $pod = pods( 'play', $post->ID );
        
        // Render the form, which will now be correctly populated.
        echo $pod->form();
    }
}

// 4. Hide unnecessary elements using CSS.
function tw_plays_hide_original_elements_css_final() {
    global $post_type;
    if ( 'play' === $post_type ) {
        // Rule 1: Hides the main block writing area ("Type / to choose a block").
        // Rule 2: Hides the native (and blank) "More Fields" Pods box to prevent duplication.
        echo '<style>
            .block-editor-writing-flow,
            #pods-meta-more-fields {
                display: none !important;
            }
        </style>';
    }
}
add_action( 'admin_head-post.php', 'tw_plays_hide_original_elements_css_final' );
add_action( 'admin_head-post-new.php', 'tw_plays_hide_original_elements_css_final' );

// 5. Enqueue assets for title-syncing and styling. (No changes needed here)
function tw_plays_enqueue_play_editor_assets_final( $hook_suffix ) {
    global $post_type;
    $pod_slugs = [ 'play', 'actor', 'crew', 'casting_record', 'board_term', 'positions' ];
    if ( ( 'post-new.php' === $hook_suffix || 'post.php' === $hook_suffix ) && in_array( $post_type, $pod_slugs ) ) {
        wp_enqueue_style( 'tw-plays-admin-styles', TW_PLAYS_URL . 'admin/assets/css/admin-styles.css', [], '1.5.0' );
        wp_enqueue_script( 'tw-plays-editor-scripts-js', TW_PLAYS_URL . 'admin/assets/js/editor-scripts.js', [ 'jquery' ], '1.5.0', true );
    }
}
add_action( 'admin_enqueue_scripts', 'tw_plays_enqueue_play_editor_assets_final' );
