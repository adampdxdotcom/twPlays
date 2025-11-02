<?php
/**
 * Customizations for the 'Play' Pod Editor Screen. (FINAL - HEADER FIX)
 *
 * This version restores the editor's top header, bringing back the "Save" button
 * and the "Back" link, while still hiding the main content area to remove the white space.
 *
 * @package TW_Plays
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPath' ) ) {
	exit;
}

// 1. Ensure post type supports 'title' in the backend.
function tw_plays_modify_play_editor_support_final() {
    add_post_type_support( 'play', 'title' );
}
add_action( 'init', 'tw_plays_modify_play_editor_support_final' );

// 2. Add our custom meta boxes.
function tw_plays_add_all_custom_meta_boxes() {
    add_meta_box( 'tw_plays_custom_title_box', 'Play Title', 'tw_plays_render_custom_title_box', 'play', 'advanced', 'high' );
    add_meta_box( 'tw_plays_custom_fields_box', 'Play Details', 'tw_plays_render_custom_meta_box_final', 'play', 'advanced', 'high' );
}
add_action( 'add_meta_boxes', 'tw_plays_add_all_custom_meta_boxes' );

// 3. Render the custom title box.
function tw_plays_render_custom_title_box( $post ) {
    echo '<div class="tw-plays-custom-title-container">';
    echo '<input type="text" name="tw_plays_custom_title_input" id="tw-plays-custom-title-input" value="' . esc_attr( $post->post_title ) . '" placeholder="Play Name Here" autocomplete="off" />';
    echo '</div>';
}

// 4. Render the Pods meta box.
function tw_plays_render_custom_meta_box_final( $post ) {
    if ( function_exists( 'pods' ) ) {
        $pod = pods( 'play', $post->ID );
        echo $pod->form();
    }
}

// 5. Inject CSS to hide the editor content area and style our new title field.
function tw_plays_hide_block_editor_and_style_title() {
    $current_screen = get_current_screen();
    if ( $current_screen && 'play' === $current_screen->post_type ) {
        echo '
        <style>
            /* Hide the default Pods meta box to prevent duplication */
            #pods-meta-more-fields { display: none !important; }

            /* Hide the main Block Editor visual area (the white space) */
            #editor .edit-post-visual-editor { display: none !important; }

            /* --- THE FIX: We NO LONGER hide the .edit-post-header --- */

            /* --- Style Our Custom Title Field --- */
            #tw_plays_custom_title_box .hndle,
            #tw_plays_custom_title_box .handle-actions { display: none; }
            #tw_plays_custom_title_box .inside { margin: 0; padding: 0; }
            #tw_plays_custom_title_box { border: none; background: transparent; }

            #tw-plays-custom-title-input {
                width: 100%;
                border: none;
                box-shadow: none;
                padding: 10px 0;
                font-size: 2em;
                font-weight: 600;
                line-height: 1.4;
                height: auto;
                background: transparent;
            }
            #tw-plays-custom-title-input:focus {
                box-shadow: none;
                outline: none;
            }
        </style>';
    }
}
add_action( 'admin_head', 'tw_plays_hide_block_editor_and_style_title' );


// 6. Enqueue assets (unchanged).
function tw_plays_enqueue_play_editor_assets_final( $hook_suffix ) {
    global $post_type;
    if ( ( 'post-new.php' === $hook_suffix || 'post.php' === $hook_suffix ) && 'play' === $post_type ) {
        wp_enqueue_script( 'tw-plays-editor-scripts-js', TW_PLAYS_URL . 'admin/assets/js/editor-scripts.js', [ 'jquery', 'wp-data' ], '1.9.0', true );
    }
}
add_action( 'admin_enqueue_scripts', 'tw_plays_enqueue_play_editor_assets_final' );
