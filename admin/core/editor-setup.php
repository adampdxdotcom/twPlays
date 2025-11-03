<?php
/**
 * Global Custom Editor Setup
 *
 * This single, configurable file replaces the individual 'play-editor.php'
 * and 'actor-editor.php' files. It applies our custom "classic editor" layout
 * to any post type defined in the configuration array.
 *
 * @package TW_Plays
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Helper function to get our custom editor configuration.
 * This is the ONLY place you need to add new post types in the future.
 *
 * @return array The configuration array.
 */
function tw_plays_get_custom_editor_config() {
    return [
        // 'post-type-slug' => 'Placeholder Text for the Title Input'
        'play'  => 'Play Name Here',
        'actor' => 'Actor Name Here',
		'crew'  => 'Crew Member Name Here',
        // Example for the future: 'crew' => 'Crew Member Name Here',
    ];
}

/**
 * 1. Disables the Block Editor for all configured post types.
 */
function tw_plays_disable_block_editors_for_custom_types() {
    $post_types = array_keys( tw_plays_get_custom_editor_config() );
    foreach ( $post_types as $post_type ) {
        remove_post_type_support( $post_type, 'editor' );
    }
}
add_action( 'init', 'tw_plays_disable_block_editors_for_custom_types', 99 );


/**
 * 2. Renders our universal custom title field at the top of the editor.
 */
function tw_plays_render_universal_title_field( $post ) {
    $config = tw_plays_get_custom_editor_config();
    // Check if the current post type is in our configuration.
    if ( ! array_key_exists( $post->post_type, $config ) ) {
        return;
    }

    // Get the correct placeholder text from our config array.
    $placeholder_text = $config[ $post->post_type ];

    echo '<div class="tw-plays-custom-title-container">';
    echo '<label for="tw-plays-custom-title-input" class="tw-plays-title-label">Title</label>';
    echo '<input type="text" name="tw_plays_custom_title_input" id="tw-plays-custom-title-input" value="' . esc_attr( $post->post_title ) . '" placeholder="' . esc_attr( $placeholder_text ) . '" autocomplete="off" />';
    echo '</div>';
}
add_action( 'edit_form_after_title', 'tw_plays_render_universal_title_field' );


/**
 * 3. Injects the necessary CSS for all configured post types.
 */
function tw_plays_universal_classic_editor_styles() {
    $current_screen = get_current_screen();
    $config = tw_plays_get_custom_editor_config();

    // Check if we are on an edit screen for one of our configured post types.
    if ( $current_screen && in_array( $current_screen->post_type, array_keys( $config ), true ) ) {
        // This CSS is generic and works for all post types.
        echo '
        <style>
            #titlediv { display: none !important; }
            .tw-plays-title-label { display: block; padding-bottom: 8px; font-weight: 600; font-size: 14px; }
            #tw-plays-custom-title-input {
                width: 100%;
                border: 1px solid #ddd;
                box-shadow: inset 0 1px 2px rgba(0,0,0,.07);
                padding: 8px;
                font-size: 1.7em;
                line-height: 100%;
                height: 1.7em;
                outline: 0;
                margin: 1px 0 20px 0;
            }
        </style>';
    }
}
add_action( 'admin_head', 'tw_plays_universal_classic_editor_styles' );


/**
 * 4. Enqueues the main editor script for all configured post types.
 */
function tw_plays_enqueue_universal_editor_assets( $hook_suffix ) {
    global $post_type;
    $config = tw_plays_get_custom_editor_config();

    // Check if we are on an edit screen for one of our configured post types.
    if ( ( 'post-new.php' === $hook_suffix || 'post.php' === $hook_suffix ) && in_array( $post_type, array_keys( $config ), true ) ) {
        // The one JS file handles all the different post types inside it.
        wp_enqueue_script( 'tw-plays-editor-scripts-js', TW_PLAYS_URL . 'admin/assets/js/editor-scripts.js', [ 'jquery' ], '2.2.0', true );
    }
}
add_action( 'admin_enqueue_scripts', 'tw_plays_enqueue_universal_editor_assets' );
