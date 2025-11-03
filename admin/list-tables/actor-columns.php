<?php
/**
 * Custom List Table Columns for the 'Actor' Pod.
 *
 * This file adds a custom "Headshot" column to the "All Actors" admin screen
 * and renders the image content for that column.
 *
 * @package TW_Plays
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * 1. REORDER & ADD Columns: Set the desired columns for the actor list table.
 */
function tw_plays_set_actor_columns( $columns ) {
    // Start fresh with a checkbox column.
    $new_columns = [
        'cb' => $columns['cb'],
    ];

    // Add our custom columns.
    $new_columns['actor_headshot'] = 'Headshot';
    $new_columns['title']          = 'Actor Name'; // The original title column

    // Add back the default date column.
    $new_columns['date'] = 'Date';

    return $new_columns;
}
add_filter( 'manage_actor_posts_columns', 'tw_plays_set_actor_columns' );


/**
 * 2. DESIGNATE PRIMARY COLUMN: Tell WordPress to put the 'Edit | Quick Edit' links under 'title'.
 */
function tw_plays_set_actor_primary_column( $default, $screen_id ) {
    // The screen ID for the actor list table is 'edit-actor'.
    if ( 'edit-actor' === $screen_id ) {
        return 'title';
    }
    return $default;
}
add_filter( 'list_table_primary_column', 'tw_plays_set_actor_primary_column', 10, 2 );


/**
 * 3. RENDER CONTENT for our custom columns.
 */
function tw_plays_render_actor_columns( $column_name, $post_id ) {
    switch ( $column_name ) {
        case 'actor_headshot':
            // Get the headshot image data from the 'headshot' field in the 'actor' pod.
            $headshot_data = pods( 'actor', $post_id )->field( 'headshot' );
            if ( ! empty( $headshot_data['guid'] ) ) {
                // Get the URL for a smaller, more appropriate size for a list table.
                $image_url = wp_get_attachment_image_url( $headshot_data['ID'], 'thumbnail' );
                // Style the image to be a uniform square for a clean look.
                echo '<img src="' . esc_url( $image_url ) . '" alt="Actor Headshot" style="height: 100px; width: 100px; object-fit: cover;"/>';
            } else {
                // If no headshot is set, display a dash.
                echo '&mdash;';
            }
            break;
        
        // We can add more cases here in the future for other custom columns.
    }
}
add_action( 'manage_actor_posts_custom_column', 'tw_plays_render_actor_columns', 10, 2 );
