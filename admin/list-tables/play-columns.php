<?php
/**
 * Custom List Table Columns for the 'Play' Pod. (v2)
 *
 * This file adds custom columns to the "All Plays" admin screen, renders their content,
 * handles the AJAX endpoint for the interactive toggle switches, and adds custom row coloring.
 *
 * @package TW_Plays
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * 1. REORDER & ADD Columns: Set the desired column order from scratch.
 */
function tw_plays_set_play_columns( $columns ) {
    // We start fresh with a checkbox column, as is standard.
    $new_columns = [
        'cb' => $columns['cb'],
    ];

    // Add our custom columns first.
    $new_columns['play_poster']        = 'Poster';
    $new_columns['title']              = 'Title'; // The original title column
    $new_columns['play_audition_status'] = 'Auditions Open';
    $new_columns['play_current_show']  = 'Current Show';
    $new_columns['play_featured']      = 'Featured';

    // Add back any other default columns we want to keep.
    $new_columns['date'] = 'Date';
    
    // Example for future: If you use a 'Views' plugin, you could re-add its column here.
    // if ( isset( $columns['views'] ) ) {
    //     $new_columns['views'] = 'Views';
    // }

    return $new_columns;
}
add_filter( 'manage_play_posts_columns', 'tw_plays_set_play_columns' );


/**
 * 2. DESIGNATE PRIMARY COLUMN: Tell WordPress to put the 'Edit | Quick Edit' links under the 'title' column.
 */
function tw_plays_set_primary_column( $default, $screen_id ) {
    if ( 'edit-play' === $screen_id ) {
        return 'title';
    }
    return $default;
}
add_filter( 'list_table_primary_column', 'tw_plays_set_primary_column', 10, 2 );


/**
 * 3. RENDER CONTENT for our custom columns.
 */
function tw_plays_render_play_columns( $column_name, $post_id ) {
    switch ( $column_name ) {
        case 'play_poster':
            $poster_data = pods( 'play', $post_id )->field( 'poster' );
            if ( ! empty( $poster_data['guid'] ) ) {
                // Get the URL for the full-sized image.
                $image_url = wp_get_attachment_image_url( $poster_data['ID'], 'full' );
                // Constrain height to 150px and let width adjust automatically.
                echo '<img src="' . esc_url( $image_url ) . '" alt="Play Poster" style="height: 150px; width: auto; max-width: 150px;"/>';
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
                esc_attr( $post_id ), esc_attr( $field_name ), esc_attr( $current_value ),
                esc_attr( $icon_class ), esc_attr( $color ), esc_html( $status_text )
            );
            break;
    }
}
add_action( 'manage_play_posts_custom_column', 'tw_plays_render_play_columns', 10, 2 );


/**
 * 4. ADD COLOR DATA to the row's HTML for JavaScript to use.
 */
function tw_plays_add_row_color_attributes( $classes, $post_id ) {
    // Only apply this logic to the 'play' post type.
    if ( 'play' === get_post_type( $post_id ) ) {
        $pod = pods( 'play', $post_id );
        $bg_color = $pod->field( 'calendar_color' );
        $text_color = $pod->field( 'calendar_color_text' );

        // If the colors exist, add them as data attributes.
        if ( ! empty( $bg_color ) ) {
            $classes[] = 'has-custom-bg-color'; // A helper class for our JS.
            $classes[] = 'data-bg-color="' . esc_attr( $bg_color ) . '"';
        }
        if ( ! empty( $text_color ) ) {
            $classes[] = 'data-text-color="' . esc_attr( $text_color ) . '"';
        }
    }
    return $classes;
}
// This is a bit of a hack, but it's the most reliable way to inject data attributes into the <tr> tag.
// We add them as "classes" and our JS will parse them.
add_filter( 'post_class', 'tw_plays_add_row_color_attributes', 10, 2 );



// =========================================================================
// == AJAX Functionality for the Toggle Switches (No changes needed here)
// =========================================================================
function tw_plays_handle_status_update() {
    // Security checks...
    if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'tw_plays_ajax_nonce' ) ) {
        wp_send_json_error( [ 'message' => 'Nonce verification failed.' ] ); return;
    }
    if ( ! current_user_can( 'edit_posts' ) ) {
        wp_send_json_error( [ 'message' => 'You do not have permission.' ] ); return;
    }

    // Sanitize and validate data...
    $post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
    $field   = isset( $_POST['field'] ) ? sanitize_key( $_POST['field'] ) : '';
    $status  = isset( $_POST['new_status'] ) ? intval( $_POST['new_status'] ) : 0;
    $allowed_fields = [ 'audition_status', 'current_show', 'featured' ];
    if ( ! $post_id || empty( $field ) || ! in_array( $field, $allowed_fields, true ) ) {
        wp_send_json_error( [ 'message' => 'Invalid data provided.' ] ); return;
    }

    // Perform the update...
    try {
        $pod = pods( 'play', $post_id );
        $pod->save( $field, $status );
        wp_send_json_success();
    } catch ( Exception $e ) {
        wp_send_json_error( [ 'message' => 'Failed to save data: ' . $e->getMessage() ] );
    }
}
add_action( 'wp_ajax_tw_plays_update_status', 'tw_plays_handle_status_update' );
