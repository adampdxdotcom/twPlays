<?php
/**
 * Customizations for the 'Play' Pod Editor Screen. (FINAL - CUSTOM TITLE FIELD)
 *
 * This version hides the entire default Block Editor interface and creates its
 * own custom title field. This provides full control over the layout, guarantees
 * the removal of white space, and creates a clean data-entry UI.
 *
 * @package TW_Plays
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 1. Ensure post type supports 'title' in the backend.
function tw_plays_modify_play_editor_support_final() {
    add_post_type_support( 'play', 'title' );
}
add_action( 'init', 'tw_plays_modify_play_editor_support_final' );

// 2. Add our custom meta boxes.
function tw_plays_add_all_custom_meta_boxes() {
    // Box 1: Our custom title field, placed at the very top.
    add_meta_box(
        'tw_plays_custom_title_box',
        'Play Title', // This title is hidden by CSS
        'tw_plays_render_custom_title_box',
        'play',
        'advanced',
        'high'
    );
    // Box 2: Our reliable Pods fields box.
    add_meta_box(
        'tw_plays_custom_fields_box',
        'Play Details',
        'tw_plays_render_custom_meta_box_final',
        'play',
        'advanced',
        'high'
    );
}
add_action( 'add_meta_boxes', 'tw_plays_add_all_custom_meta_boxes' );

/**
 * 3. Render the content of our custom title box.
 * This is just a large input field styled to look like the default title.
 */
function tw_plays_render_custom_title_box( $post ) {
    echo '<div class="tw-plays-custom-title-container">';
    echo '<input type="text" name="tw_plays_custom_title_input" id="tw-plays-custom-title-input" value="' . esc_attr( $post->post_title ) . '" placeholder="Play Name Here" autocomplete="off" />';
    echo '</div>';
}

/**
 * 4. Render the content of our Pods meta box.
 */
function tw_plays_render_custom_meta_box_final( $post ) {
    if ( function_exists( 'pods' ) ) {
        $pod = pods( 'play', $post->ID );
        echo $pod->form();
    }
}

// 5. Inject CSS to completely hide the default editor and style our new title field.
function tw_plays_hide_block_editor_and_style_title() {
    $current_screen = get_current_screen();
    if ( $current_screen && 'play' === $current_screen->post_type ) {
        echo '
        <style>
		    #pods-meta-more-fields {
                display: none !important;
            }
            /* Hide the entire Block Editor main area */
            #editor .edit-post-visual-editor,
            /* Hide the top bar with the block tools */
            .edit-post-header {
                display: none !important;
            }

            /* --- Style Our Custom Title Field --- */
            /* Hide the meta box chrome (title, border) around our custom title */
            #tw_plays_custom_title_box .hndle,
            #tw_plays_custom_title_box .handle-actions { display: none; }
            #tw_plays_custom_title_box .inside { margin: 0; padding: 0; }
            #tw_plays_custom_title_box { border: none; background: transparent; }

            /* Style the input itself to look like the real title */
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
