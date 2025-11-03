<?php
/**
 * Customizations for the 'Actor' Pod Editor Screen.
 *
 * This file disables the Block Editor for the 'actor' post type to provide a
 * stable, simple data-entry screen. It creates a custom title input for
 * aesthetics and relies on the native Pods meta box for all other fields.
 *
 * @package TW_Plays
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 1. Disable the Block Editor for the 'actor' post type.
// We add a high priority (99) to ensure this runs AFTER the post type is registered.
function tw_plays_disable_block_editor_for_actor() {
    remove_post_type_support( 'actor', 'editor' );
}
add_action( 'init', 'tw_plays_disable_block_editor_for_actor', 99 );

// 2. Inject our custom title field directly after the original title area.
function tw_plays_render_actor_title_field( $post ) {
    // We only want this to run for our 'actor' post type.
    if ( 'actor' !== $post->post_type ) {
        return;
    }

    // This HTML is identical to the 'play' version, but with a different placeholder.
    echo '<div class="tw-plays-custom-title-container">';
    echo '<label for="tw-plays-custom-title-input" class="tw-plays-title-label">Title</label>';
    echo '<input type="text" name="tw_plays_custom_title_input" id="tw-plays-custom-title-input" value="' . esc_attr( $post->post_title ) . '" placeholder="Actor Name Here" autocomplete="off" />';
    echo '</div>';
}
add_action( 'edit_form_after_title', 'tw_plays_render_actor_title_field' );

// 3. Inject CSS to hide the original title box and style our new one.
function tw_plays_actor_classic_editor_styles() {
    $current_screen = get_current_screen();
    if ( $current_screen && 'actor' === $current_screen->post_type ) {
        echo '
        <style>
            /* HIDE the original title box */
            #titlediv {
                display: none !important;
            }

            /* STYLE our new custom label */
            .tw-plays-title-label {
                display: block;
                padding-bottom: 8px;
                font-weight: 600;
                font-size: 14px;
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
add_action( 'admin_head', 'tw_plays_actor_classic_editor_styles' );

// 4. Enqueue our JavaScript.
// Note: The main JS file is enqueued for all pods, so we just need to ensure the hook is right.
function tw_plays_enqueue_actor_editor_assets( $hook_suffix ) {
    global $post_type;
    if ( ( 'post-new.php' === $hook_suffix || 'post.php' === $hook_suffix ) && 'actor' === $post_type ) {
        // We can use the same JS file as before. We will add a new block to it for the actor pod.
        wp_enqueue_script( 'tw-plays-editor-scripts-js', TW_PLAYS_URL . 'admin/assets/js/editor-scripts.js', [ 'jquery' ], '2.2.0', true );
    }
}
add_action( 'admin_enqueue_scripts', 'tw_plays_enqueue_actor_editor_assets' );
