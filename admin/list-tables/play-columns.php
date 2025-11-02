<?php
/**
 * Custom List Table Columns for the 'Play' Pod.
 *
 * This file adds custom columns to the "All Plays" admin screen and renders their content.
 * It also sets up the necessary HTML structure for the AJAX toggle functionality.
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
    // Create a new array to reorder columns and insert our own.
    $new_columns = [];

    foreach ( $columns as $key => $title ) {
        // Add original columns up to the 'title' column.
        $new_columns[ $key ] = $title;
        if ( 'title' === $key ) {
            // Add our custom columns right after the title.
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
    
    // Use a switch statement to handle our different custom columns.
    switch ( $column_name ) {

        case 'play_poster':
            $poster_data = pods( 'play', $post_id )->field( 'poster' );
            if ( ! empty( $poster_data['guid'] ) ) {
                // Get the thumbnail URL for efficiency.
                $thumbnail_url = wp_get_attachment_image_url( $poster_data['ID'], 'thumbnail' );
                echo '<img src="' . esc_url( $thumbnail_url ) . '" width="100" alt="Play Poster" style="max-width: 100px; height: auto;"/>';
            } else {
                echo '&mdash;'; // Output an em-dash if no poster is set.
            }
            break;

        // The next three cases all handle our yes/no toggle fields.
        case 'play_audition_status':
        case 'play_current_show':
        case 'play_featured':
            // Extract the field name from the column name (e.g., 'play_featured' -> 'featured').
            $field_name = str_replace( 'play_', '', $column_name );
            
            // Get the current value from the Pods field (1 for 'yes', 0 for 'no').
            $current_value = (int) pods( 'play', $post_id )->field( $field_name );
            
            // Determine which icon and status text to show.
            $is_active      = ( 1 === $current_value );
            $icon_class     = $is_active ? 'yes-alt' : 'dismiss';
            $status_text    = $is_active ? 'Yes' : 'No';
            $color          = $is_active ? 'green' : '#a0a5aa'; // WordPress grey
            
            // This is the setup for our future AJAX toggle.
            // We output a clickable link with all the data our JavaScript will need.
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
