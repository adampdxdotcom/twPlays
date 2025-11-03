<?php
/**
 * Custom List Table Columns for the 'Actor' Pod. (v2.3 - RELIABLE QUERY METHOD)
 *
 * This version uses the definitive field names confirmed by screenshots and
 * implements a more reliable, step-by-step method for checking the board
 * position, guaranteeing an accurate result.
 *
 * @package TW_Plays
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * 1. ADD CSS to adjust the column widths for a cleaner look.
 */
function tw_plays_actor_column_styles() {
    $current_screen = get_current_screen();
    if ( $current_screen && 'edit-actor' === $current_screen->id ) {
        echo '<style>
            .column-actor_headshot { width: 120px; }
        </style>';
    }
}
add_action( 'admin_head', 'tw_plays_actor_column_styles' );


/**
 * 2. REORDER & ADD Columns: Add the new "Current Activity" column.
 */
function tw_plays_set_actor_columns( $columns ) {
    $new_columns = [ 'cb' => $columns['cb'] ];
    $new_columns['actor_headshot']         = 'Headshot';
    $new_columns['title']                  = 'Actor Name';
    $new_columns['actor_current_activity'] = 'Current Activity';
    $new_columns['date']                   = 'Date';
    return $new_columns;
}
add_filter( 'manage_actor_posts_columns', 'tw_plays_set_actor_columns' );


/**
 * 3. DESIGNATE PRIMARY COLUMN: Unchanged.
 */
function tw_plays_set_actor_primary_column( $default, $screen_id ) {
    if ( 'edit-actor' === $screen_id ) {
        return 'title';
    }
    return $default;
}
add_filter( 'list_table_primary_column', 'tw_plays_set_actor_primary_column', 10, 2 );


/**
 * 4. RENDER CONTENT for all our custom columns.
 */
function tw_plays_render_actor_columns( $column_name, $post_id ) {
    switch ( $column_name ) {

        case 'actor_headshot':
            $headshot_data = pods( 'actor', $post_id )->field( 'headshot' );
            if ( ! empty( $headshot_data['guid'] ) ) {
                $image_url = wp_get_attachment_image_url( $headshot_data['ID'], 'thumbnail' );
                echo '<img src="' . esc_url( $image_url ) . '" alt="Actor Headshot" style="height: 100px; width: 100px; object-fit: cover; border-radius: 4px;"/>';
            } else {
                echo '&mdash;';
            }
            break;
        
        case 'actor_current_activity':
            $activity_lines = [];

            // --- Query 1: Find the actor's current play ---
            // This query uses the field names confirmed in your screenshot: 'actor' and 'play'.
            $play_params = [
                'limit' => 1,
                'where' => [
                    [ 'key' => 'actor.ID', 'value' => $post_id ],
                    [
                        'key'      => 'play.post_status',
                        'value'    => 'publish',
                        'relation' => 'AND',
                        'where'    => [
                            [ 'key' => 'current_show', 'value' => 1 ],
                            [ 'key' => 'audition_status', 'value' => 1, 'relation' => 'OR' ],
                        ],
                    ],
                ],
            ];
            $casting_records = pods( 'casting_record' )->find( $play_params );

            if ( $casting_records->total() > 0 ) {
                $play_title = $casting_records->field( 'play.post_title' );
                if ( ! empty( $play_title ) ) {
                    $activity_lines[] = '<strong>Working on:</strong> ' . esc_html( $play_title );
                }
            }

            // --- Query 2: Find the actor's current board position (RELIABLE METHOD) ---
            // This query uses the field names confirmed in your screenshots: 'board_member_name', 'board_position', 'is_board'.
            $board_params = [
                'limit' => 1,
                'where' => [
                    [ 'key' => 'board_member_name.ID', 'value' => $post_id ],
                    [ 'key' => 'start_date', 'value' => date('Y-m-d'), 'compare' => '<=', 'type' => 'DATE' ],
                    [ 'key' => 'end_date', 'value' => date('Y-m-d'), 'compare' => '>=', 'type' => 'DATE' ],
                ],
            ];
            $board_terms = pods( 'board_term' )->find( $board_params );

            // ** THE NEW RELIABLE LOGIC **
            // First, we find an active term. THEN we check if their position is an elected one.
            if ( $board_terms->total() > 0 ) {
                // We have to call fetch() to load the first result into memory.
                $board_terms->fetch();
                
                // Now we can traverse the relationship to check the 'is_board' field.
                $is_elected_board_member = $board_terms->field( 'board_position.is_board' );

                // The 'is_board' field is a Yes/No, which Pods stores as 1 for Yes.
                if ( 1 == $is_elected_board_member ) {
                    $position_title = $board_terms->field( 'board_position.post_title' );
                    if ( ! empty( $position_title ) ) {
                        $activity_lines[] = '<strong>Board Position:</strong> ' . esc_html( $position_title );
                    }
                }
            }

            // Display the results.
            if ( empty( $activity_lines ) ) {
                echo '&mdash;';
            } else {
                echo implode( '<br>', $activity_lines );
            }
            break;
    }
}
add_action( 'manage_actor_posts_custom_column', 'tw_plays_render_actor_columns', 10, 2 );
