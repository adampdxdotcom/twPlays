<?php
/**
 * Plugin Initializer
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

// =========================================================================
// == General Admin Pages
// =========================================================================
require_once TW_PLAYS_PATH . 'admin/menu.php';
require_once TW_PLAYS_PATH . 'admin/dashboard-page.php';
require_once TW_PLAYS_PATH . 'admin/core/editor-setup.php';


// =========================================================================
// == Play Pod Customizations
// =========================================================================
require_once TW_PLAYS_PATH . 'admin/list-tables/play-columns.php';


// =========================================================================
// == Actor Pod Customizations
// =========================================================================
require_once TW_PLAYS_PATH . 'admin/list-tables/actor-columns.php';


// =========================================================================
// == Centralized Logging to TW Status Center (NEWLY ADDED)
// =========================================================================

/**
 * Logs when a Play, Actor, or Crew post is created or updated.
 *
 * This function is attached to the 'save_post' hook.
 *
 * @param int     $post_id The ID of the post being saved.
 * @param WP_Post $post    The post object.
 * @param bool    $update  Whether this is an update to an existing post.
 */
function tw_plays_log_post_changes( $post_id, $post, $update ) {
	// 1. Check if our logging function even exists. If not, stop.
	if ( ! function_exists( 'tw_suite_log' ) ) {
		return;
	}

	// 2. Define the post types we care about.
	$relevant_post_types = [ 'play', 'actor', 'crew' ];
	if ( ! in_array( $post->post_type, $relevant_post_types, true ) ) {
		return;
	}

	// 3. Ignore auto-saves and revisions to prevent log spam.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( wp_is_post_revision( $post_id ) ) {
		return;
	}
    // 4. Make sure the post status is 'publish' to avoid logging drafts etc.
    if ( 'publish' !== $post->post_status ) {
        return;
    }

	// 5. Get the user who made the change.
	$user = get_user_by( 'id', $post->post_author );
	$user_name = $user ? $user->display_name : 'System';

	// 6. Get the singular name of the post type for a clean message.
	$post_type_object = get_post_type_object( $post->post_type );
	$post_type_label = $post_type_object ? $post_type_object->labels->singular_name : ucfirst( $post->post_type );

	// 7. Determine if it was a creation or an update.
	$action = $update ? 'updated' : 'created';

	// 8. Construct the final log message.
	$message = sprintf(
		'%s "%s" was %s by %s.',
		$post_type_label,
		esc_html( $post->post_title ),
		$action,
		$user_name
	);

	// 9. Send the log to the Status Center.
	tw_suite_log( 'TW Plays', $message, 'INFO' );
}
// Hook into save_post with 3 arguments. The priority of 10 is standard.
add_action( 'save_post', 'tw_plays_log_post_changes', 10, 3 );


/**
 * Logs when a Play, Actor, or Crew post is deleted.
 *
 * This function is attached to the 'before_delete_post' hook, which is better
 * than 'delete_post' because the post object still exists.
 *
 * @param int $post_id The ID of the post being deleted.
 */
function tw_plays_log_deleted_post( $post_id ) {
	// 1. Check if our logging function exists.
	if ( ! function_exists( 'tw_suite_log' ) ) {
		return;
	}

	// 2. Get the post object before it's gone.
	$post = get_post( $post_id );
	if ( ! $post ) {
		return;
	}

	// 3. Define the post types we care about.
	$relevant_post_types = [ 'play', 'actor', 'crew' ];
	if ( ! in_array( $post->post_type, $relevant_post_types, true ) ) {
		return;
	}

	// 4. Get the user who is performing the deletion.
	$user = wp_get_current_user();
	$user_name = $user ? $user->display_name : 'System';

	// 5. Get the singular name of the post type.
	$post_type_object = get_post_type_object( $post->post_type );
	$post_type_label = $post_type_object ? $post_type_object->labels->singular_name : ucfirst( $post->post_type );

	// 6. Construct the final message. We use 'WARNING' as the level because deletion is a significant event.
	$message = sprintf(
		'%s "%s" was deleted by %s.',
		$post_type_label,
		esc_html( $post->post_title ),
		$user_name
	);

	// 7. Send the log.
	tw_suite_log( 'TW Plays', $message, 'WARNING' );
}
// Hook into 'before_delete_post' to catch deletions.
add_action( 'before_delete_post', 'tw_plays_log_deleted_post' );
