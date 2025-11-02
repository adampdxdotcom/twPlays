<?php
/**
 * Customizations for the 'Play' Pod Editor Screen. (FINAL - CLASSIC EDITOR METHOD)
 *
 * This version disables the Block Editor to provide a stable, simple data-entry screen.
 * It creates a custom title input for aesthetics and relies on the native Pods
 * meta box, which now saves correctly without any custom save functions.
 *
 * @package TW_Plays
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 1. Disable the Block Editor for the 'play' post type.
// This is the key to a stable layout and reliable saving.
function tw_plays_disable_block_editor_for_play() {
    remove_post_type_support( 'play', 'editor' );
}
add_action( 'init', 'tw_plays_disable_block_editor_for_play' );

// 2. Add our custom meta box for the large title input.
function tw_plays_add_custom_title_meta_box() {
    add_meta_box(
        'tw_plays_custom_title_box',
        'Play Title', // This title is hidden by CSS
        'tw_plays_render_custom_title_box',
        'play',
        'advanced', // This context places it in the main column
        'high'
    );
}
add_action( 'add_meta_boxes', 'tw_plays_add_custom_title_meta_box' );

/**
 * 3. Render the content of our custom title box.
 * This is just a large input field styled to look like the default title.
 */
function tw_plays_render_custom_title_box( $post ) {
    echo '<div class="tw-plays-custom-title-container">';
    echo '<input type="text" name="tw_plays_custom_title_input" id="tw-plays-custom-title-input" value="' . esc_attr( $post->post_title ) . '" placeholder="Play Name Here" autocomplete="off" />';
    echo '</div>';
}

// 4. Inject CSS to hide the original title box and style our new one.
function tw_plays_classic_editor_styles() {
    $current_screen = get_current_screen();
    if ( $current_screen && 'play' === $current_screen->post_type ) {
        echo '
        <style>
            /* --- HIDE THE ORIGINAL TITLE BOX --- */
            #titlediv {
                display: none !important;
            }

            /* --- STYLE OUR CUSTOM TITLE FIELD --- */
            /* Hide the meta box chrome (title, border) around our custom title */
            #tw_plays_custom_title_box .hndle,
            #tw_plays_custom_title_box .handle-actions { display: none; }
            #tw_plays_custom_title_box .inside { margin: 0; padding: 0; }
            #tw_plays_custom_title_box { border: none; background: transparent; box-shadow: none; }

            /* Style the input itself to look like the real title */
            #tw-plays-custom-title-input {
                width: 100%;
                border: 1px solid #ddd;
                box-shadow: inset 0 1px 2px rgba(0,0,0,.07);
                padding: 8px;
                font-size: 1.7em;
                line-height: 100%;
                height: 1.7em;
                outline: 0;
                margin: 1px 0;
            }
        </style>';
    }
}
add_action( 'admin_head', 'tw_plays_classic_editor_styles' );


// 5. Enqueue our JavaScript.
function tw_plays_enqueue_play_editor_assets_final( $hook_suffix ) {
    global $post_type;
    if ( ( 'post-new.php' === $hook_suffix || 'post.php' === $hook_suffix ) && 'play' === $post_type ) {
        wp_enqueue_script( 'tw-plays-editor-scripts-js', TW_PLAYS_URL . 'admin/assets/js/editor-scripts.js', [ 'jquery' ], '2.0.0', true );
    }
}
add_action( 'admin_enqueue_scripts', 'tw_plays_enqueue_play_editor_assets_final' );
