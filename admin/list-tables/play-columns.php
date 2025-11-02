<?php
/**
 * Custom List Table Columns for the 'Play' Pod.
 *
 * This file adds custom columns to the "All Plays" admin screen, renders their content,
 * and handles the AJAX endpoint for the interactive toggle switches.
 *
 * @package TW_Plays
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * 1. Add custom columns to the 'play' post type list table.
 */
function tw_plays_add_play_columns( $columns ) {
    $new_columns = [];
    foreach ( $columns as $key => $title ) {
        $new_columns[ $key ] = $title;
        if ( 'title' === $key ) {
            $new_columns['play_poster']        = 'Poster';
            $new_columns['play_audition_status'] = 'Auditions Open';
            $new_columns['play_current_show']  = 'Current Show';
            $new_columns['play_featured']      = 'Featured';
        }
    }
    return $new_columns;
}
add_filter( 'manage_play_posts_columns', 'tw_plays_add_play_columns' );


/**
 * 2. Render the content for our custom columns.
 */
function tw_plays_render_play_columns( $column_name, $post_id ) {
    switch ( $column_name ) {
        case 'play_poster':
            $poster_data = pods( 'play', $post_id )->field( 'poster' );
            if ( ! empty( $poster_data['guid'] ) ) {
                $thumbnail_url = wp_get_attachment_image_url( $poster_data['ID'], 'thumbnail' );
                echo '<img src="' . esc_url( $thumbnail_url ) . '" width="100" alt="Play Poster" style="max-width: 100px; height: auto;"/>';
            } else {
                echo '&mdash;';
            }
            break;

        case 'play_audition_status':
        case 'play_current_show':
        case 'play_featured':
            $field_name = str_replace( 'play_', '', $column_name );
            $current_value = (int) pods( 'play', $post_id )->field( $field_name );
            $is_active      = ( 1 === $current_value );
            $icon_class     = $is_active ? 'yes-alt' : 'dismiss';
            $status_text    = $is_active ? 'Yes' : 'No';
            $color          = $is_active ? 'green' : '#a0a5aa';
            
            printf(
                '<a href="#" class="tw-plays-toggle" data-post-id="%d" data-field="%s" data-current-status="%d" title="Click to toggle">
                    <span class="dashicons dashicons-%s" style="color: %s; font-size: 24px;"></span>
                    <span class="screen-reader-text">%s</span>
                </a>',
                esc_attr( $post_id ),
                esc_attr( $field_name ),
                esc_attr( $current_value ),
                esc_attr( $icon_class ),
                esc_attr( $color ),
                esc_html( $status_text )
            );
            break;
    }
}
add_action( 'manage_play_posts_custom_column', 'tw_plays_render_play_columns', 10, 2 );


// =========================================================================
// == NEW: AJAX Functionality for the Toggle Switches
// =========================================================================

/**
 * 3. The server-side function that handles the AJAX request from our JavaScript.
 */
function tw_plays_handle_status_update() {
    // 1. Security Check: Verify the nonce.
    if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'tw_plays_ajax_nonce' ) ) {
        wp_send_json_error( [ 'message' => 'Nonce verification failed.' ] );
        return;
    }

    // 2. Security Check: Ensure the current user has permission to edit posts.
    if ( ! current_user_can( 'edit_posts' ) ) {
        wp_send_json_error( [ 'message' => 'You do not have permission to perform this action.' ] );
        return;
    }

    // 3. Sanitize and validate the incoming data.
    $post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
    $field   = isset( $_POST['field'] ) ? sanitize_key( $_POST['field'] ) : '';
    $status  = isset( $_POST['new_status'] ) ? intval( $_POST['new_status'] ) : 0;

    // A list of fields that are allowed to be updated via this AJAX call.
    $allowed_fields = [ 'audition_status', 'current_show', 'featured' ];

    if ( ! $post_id || empty( $field ) || ! in_array( $field, $allowed_fields, true ) ) {
        wp_send_json_error( [ 'message' => 'Invalid data provided.' ] );
        return;
    }

    // 4. Perform the update using the Pods API.
    try {
        $pod = pods( 'play', $post_id );
        $pod->save( $field, $status );
        
        // If the save was successful, send a success response.
        wp_send_json_success();

    } catch ( Exception $e ) {
        // If there was an error saving, send an error response.
        wp_send_json_error( [ 'message' => 'Failed to save data: ' . $e->getMessage() ] );
    }
}
// Hook our function into WordPress's AJAX system for logged-in users.
add_action( 'wp_ajax_tw_plays_update_status', 'tw_plays_handle_status_update' );
