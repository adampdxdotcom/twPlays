<?php
/**
 * Customizations for the 'Play' Pod Editor Screen. (v6 - Road B Final)
 *
 * This version creates a unified data entry experience by adding our own
 * 'post_title' input inside our custom meta box and hiding the original.
 *
 * @package TW_Plays
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * 1. Modify post type support for 'play'. We remove the editor, but keep 'title'.
 *    This is necessary for the backend to correctly save our custom title field.
 */
function tw_plays_modify_play_editor_support_v6() {
    remove_post_type_support( 'play', 'editor' );
    add_post_type_support( 'play', 'title' ); // Ensure 'title' support is present
}
add_action( 'init', 'tw_plays_modify_play_editor_support_v6' );


/**
 * 2. Add our own custom meta box to the 'play' editor screen.
 */
function tw_plays_add_custom_meta_box_v6() {
    add_meta_box(
        'tw_plays_custom_fields_box',
        'Play Details',
        'tw_plays_render_custom_meta_box_v6',
        'play',
        'advanced', // The main column
        'high'      // At the top
    );
}
add_action( 'add_meta_boxes', 'tw_plays_add_custom_meta_box_v6' );


/**
 * 3. Render the content of our custom meta box.
 *    This now includes our own Title field, followed by the Pods fields.
 */
function tw_plays_render_custom_meta_box_v6( $post ) {
    ?>
    <div class="tw-plays-editor-container">
        <!-- Manually create our own "Play Title" field -->
        <div class="tw-plays-editor-field">
            <label for="tw-plays-post-title" class="tw-plays-editor-label">Play Title</label>
            <input type="text" 
                   name="post_title" 
                   id="tw-plays-post-title"
                   class="tw-plays-editor-title-input"
                   value="<?php echo esc_attr( $post->post_title ); ?>" 
                   placeholder="Enter Play Name Here">
        </div>

        <?php
        // Now, render the Pods form fields below our custom title field.
        if ( function_exists( 'pods' ) ) {
            $pod = pods( 'play', $post->ID );
            echo $pod->form();
        }
        ?>
    </div>
    <?php
}


/**
 * 4. Hide the now-redundant original elements using CSS.
 */
function tw_plays_hide_original_elements_css_v6() {
    global $post_type;

    if ( 'play' === $post_type ) {
        echo '
        <style>
            /* Hide the original WordPress title wrapper */
            .edit-post-visual-editor__post-title-wrapper {
                display: none !important;
            }
            /* Hide the block editor (belt-and-suspenders approach) */
            .block-editor-writing-flow {
                display: none !important;
            }
            /* Hide the original Pods meta box to prevent duplicates */
            #pods-meta-more-fields { 
                display: none !important;
            }
        </style>
        ';
    }
}
add_action( 'admin_head-post.php', 'tw_plays_hide_original_elements_css_v6' );
add_action( 'admin_head-post-new.php', 'tw_plays_hide_original_elements_css_v6' );


/**
 * 5. Enqueue our stylesheet for the editor screen.
 */
function tw_plays_enqueue_play_editor_assets_v6( $hook_suffix ) {
    global $post_type;

    if ( ( 'post-new.php' === $hook_suffix || 'post.php' === $hook_suffix ) && 'play' === $post_type ) {
        wp_enqueue_style(
            'tw-plays-play-editor-styles',
            TW_PLAYS_URL . 'admin/assets/css/admin-styles.css',
            [],
            '1.3.0' // Version bump
        );
    }
}
add_action( 'admin_enqueue_scripts', 'tw_plays_enqueue_play_editor_assets_v6' );
