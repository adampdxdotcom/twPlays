<?php
/**
 * Customizations for the 'Play' Pod Editor Screen. (v7 - Definitive Fix)
 *
 * This version manually creates the hidden #title field to ensure
 * the JavaScript sync and the "Publish" button always work.
 *
 * @package TW_Plays
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// 1. Modify post type support for 'play'. (No changes needed here)
function tw_plays_modify_play_editor_support_v7() {
    remove_post_type_support( 'play', 'editor' );
    add_post_type_support( 'play', 'title' );
}
add_action( 'init', 'tw_plays_modify_play_editor_support_v7' );

// 2. Add our custom meta box. (No changes needed here)
function tw_plays_add_custom_meta_box_v7() {
    add_meta_box( 'tw_plays_custom_fields_box', 'Play Details', 'tw_plays_render_custom_meta_box_v7', 'play', 'advanced', 'high' );
}
add_action( 'add_meta_boxes', 'tw_plays_add_custom_meta_box_v7' );

/**
 * 3. Render the content of our custom meta box.
 *    CRUCIAL CHANGE: We now add our own hidden #title field.
 */
function tw_plays_render_custom_meta_box_v7( $post ) {
    ?>
    <div class="tw-plays-editor-container">
        <!-- Manually create our visible "Play Title" field -->
        <div class="tw-plays-editor-field">
            <label for="tw-plays-post-title" class="tw-plays-editor-label">Play Title</label>
            <input type="text" 
                   name="post_title" 
                   id="tw-plays-post-title"
                   class="tw-plays-editor-title-input"
                   value="<?php echo esc_attr( $post->post_title ); ?>" 
                   placeholder="Enter Play Name Here">
        </div>

        <!-- 
            THE FIX: Manually create a hidden input with the ID and name 'title'.
            This gives the "Publish" button's JavaScript the target it's looking for.
            Our own JS will keep this field's value in sync.
        -->
        <input type="hidden" id="title" name="title" value="<?php echo esc_attr( $post->post_title ); ?>">

        <?php
        // Now, render the Pods form fields below.
        if ( function_exists( 'pods' ) ) {
            $pod = pods( 'play', $post->ID );
            echo $pod->form();
        }
        ?>
    </div>
    <?php
}

// 4. Hide original elements. (No changes needed here)
function tw_plays_hide_original_elements_css_v7() {
    global $post_type;
    if ( 'play' === $post_type ) {
        echo '<style>.edit-post-visual-editor__post-title-wrapper, .block-editor-writing-flow, #pods-meta-more-fields { display: none !important; }</style>';
    }
}
add_action( 'admin_head-post.php', 'tw_plays_hide_original_elements_css_v7' );
add_action( 'admin_head-post-new.php', 'tw_plays_hide_original_elements_css_v7' );

// 5. Enqueue stylesheet. (No changes needed here)
function tw_plays_enqueue_play_editor_assets_v7( $hook_suffix ) {
    global $post_type;
    if ( ( 'post-new.php' === $hook_suffix || 'post.php' === $hook_suffix ) && 'play' === $post_type ) {
        wp_enqueue_style( 'tw-plays-play-editor-styles', TW_PLAYS_URL . 'admin/assets/css/admin-styles.css', [], '1.3.1' );
    }
}
add_action( 'admin_enqueue_scripts', 'tw_plays_enqueue_play_editor_assets_v7' );
