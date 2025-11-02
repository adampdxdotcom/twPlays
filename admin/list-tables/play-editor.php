<?php
/**
 * DEBUGGING TEST for the 'Play' Editor Screen.
 *
 * This file's only purpose is to print a visible message on the screen
 * to confirm that our hooks are firing correctly.
 *
 * @package TW_Plays
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Hook into the 'Add New' or 'Edit' screen for the 'play' post type
 * and display a highly visible debugging message.
 */
function tw_plays_editor_debug_test( $post ) {
    // Only run this test on our 'play' post type.
    if ( 'play' !== $post->post_type ) {
        return;
    }

    // Print a big, ugly, obvious message.
    echo '<div style="background-color: #ff0000; color: #ffffff; padding: 20px; font-size: 24px; font-weight: bold; text-align: center; border: 5px solid #000;">';
    echo 'DEBUG TEST SUCCESSFUL: The play-editor.php hooks are working!';
    echo '</div>';
}
// 'edit_form_top' is a very reliable hook that fires right after the opening <form> tag on the editor screen.
add_action( 'edit_form_top', 'tw_plays_editor_debug_test' );
