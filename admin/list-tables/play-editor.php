<?php
/**
 * Customizations for the 'Play' Pod Editor Screen. (FINAL - LAYOUT FIX)
 *
 * This version corrects the hook priority to reliably disable the Block Editor.
 * It also uses the 'edit_form_after_title' action to place the custom title
 * input correctly at the top of the screen.
 *
 * @package TW_Plays
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 1. Disable the Block Editor for the 'play' post type.
// We add a high priority (99) to ensure this runs AFTER the post type is registered.
function tw_plays_disable_block_editor_for_play() {
    remove_post_type_support( 'play', 'editor' );
}
add_action( 'init', 'tw_plays_disable_block_editor_for_play', 99 );

// 2. NEW METHOD: Inject our custom title field directly after the original title area.
// This is the correct way to place it at the top of the content column.
function tw_plays_render_custom_title_field_high( $post ) {
    // We only want this to run for our 'play' post type.
    if ( 'play' !== $post->post_type ) {
        return;
    }
    echo '<div class="tw-plays-custom-title-container">';
    echo '<input type="text" name="tw_plays_custom_title_input" id="tw-plays-custom-title-input" value="' . esc_attr( $post->post_title ) . '" placeholder="Play Name Here" autocomplete="off" />';
    echo '</div>';
}
add_action( 'edit_form_after_title', 'tw_plays_render_custom_title_field_high' );

// 3. Inject CSS to hide the original title box and style our new one.
function tw_plays_classic_editor_styles() {
    $current_screen = get_current_screen();
    if ( $current_screen && 'play' === $current_screen->post_type ) {
        echo '
        <style>
            /* HIDE the original title box */
            #titlediv {
                display: none !important;
            }

            /* STYLE our custom title input */
            #tw-plays-custom-title-input {
                width: 100%;
                border: 1px solid #ddd;
                box-shadow: inset 0 1px 2px rgba(0,0,0,.07);
                padding: 8px;
                font-size: 1.7em;
                line-height: 100%;
                height: 1.7em;
                outline: 0;
                margin: 1px 0 20px 0; /* Add some space below the title */
            }
        </style>';
    }
}
add_action( 'admin_head', 'tw_plays_classic_editor_styles' );

// 4. Enqueue our JavaScript.
function tw_plays_enqueue_play_editor_assets_final( $hook_suffix ) {
    global $post_type;
    if ( ( 'post-new.php' === $hook_suffix || 'post.php' === $hook_suffix ) && 'play' === $post_type ) {
        wp_enqueue_script( 'tw-plays-editor-scripts-js', TW_PLAYS_URL . 'admin/assets/js/editor-scripts.js', [ 'jquery' ], '2.1.0', true );
    }
}
add_action( 'admin_enqueue_scripts', 'tw_plays_enqueue_play_editor_assets_final' );
