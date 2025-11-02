<?php
/**
 * ISOLATED DEBUGGING TEST for the 'Actor' Editor Screen.
 *
 * @package TW_Plays
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Hook into the 'Add New' or 'Edit' screen for the 'actor' post type
 * and display a highly visible debugging message.
 */
function tw_plays_actor_debug_test( $post ) {
    // Only run this test on our 'actor' post type.
    if ( 'actor' !== $post->post_type ) {
        return;
    }

    // Print a big, ugly, obvious BLUE box this time.
    echo '<div style="background-color: #007cba; color: #ffffff; padding: 20px; font-size: 24px; font-weight: bold; text-align: center; border: 5px solid #000;">';
    echo 'ACTOR DEBUG TEST SUCCESSFUL: The hooks for the Actor editor are working!';
    echo '</div>';
}
add_action( 'edit_form_top', 'tw_plays_actor_debug_test' );
