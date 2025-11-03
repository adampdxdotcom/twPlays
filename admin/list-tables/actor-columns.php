<?php
/**
 * Custom List Table Columns for the 'Actor' Pod. (v2 with Current Activity)
 *
 * This file adds a "Headshot" column, a "Current Activity" column, and handles
 * the complex queries required to display an actor's current play and board status.
 * It also includes CSS to adjust column widths for a better layout.
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
    // Only apply these styles on the "All Actors" list table screen.
    if ( $current_screen && 'edit-actor' === $current_screen->id ) {
        echo '<style>
            /* Give the headshot column a fixed, narrow width */
            .column-actor_headshot {
                width: 120px;
            }
        </style>';
    }
}
add_action( 'admin_head', 'tw_plays_actor_column_styles' );


/**
 * 2. REORDER & ADD Columns: Add the new "Current Activity" column.
 */
function tw_plays_set_actor_columns( $columns ) {
    $new_columns = [
        'cb' => $columns['cb'],
    ];

    // Define our new column order.
    $new_columns['actor_headshot']         = 'Headshot';
    $new_columns['title']                  = 'Actor Name';
    $new_columns['actor_current_activity'] = 'Current Activity'; // Our new column
    $new_columns['date']                   = 'Date';

    return $new_columns;
}
add_filter( 'manage_actor_posts_columns', 'tw_plays_set_actor_columns' );


/**
 * 3. DESIGNATE PRIMARY COLUMN: Unchanged, still the 'title' column.
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
            // This is where we run our smart queries.
            $activity_lines = []; // An array to hold the lines of text we find.

            // --- Query 1: Find the actor's current play ---
            $play_params = [
                'limit' => 1, // We only need to find one.
                'where' => [
                    [
                        'key'   => 'actor_name.ID', // Assuming 'casting_record' has a field named 'actor_name' linked to the actor.
                        'value' => $post_id,
                    ],
                    [ // This is a sub-query to check the related play's status.
                        'key'      => 'play.post_status',
                        'value'    => 'publish',
                        'relation' => 'AND',
                        'where'    => [
                            [
                                'key'     => 'current_show',
                                'value'   => 1,
                                'compare' => '=',
                            ],
                            [
                                'key'      => 'audition_status',
                                'value'    => 1,
                                'compare'  => '=',
                                'relation' => 'OR', // It's a current show OR auditions are open.
                            ],
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

            // --- Query 2: Find the actor's current board position ---
            $board_params = [
                'limit' => 1,
                'where' => [
                    [
                        'key'   => 'board_member_name.ID', // This comes from your template code.
                        'value' => $post_id,
                    ],
                    [
                        'key'     => 'start_date',
                        'value'   => 'NOW',
                        'compare' => '<=',
                    ],
                    [
                        'key'     => 'end_date',
                        'value'   => 'NOW',
                        'compare' => '>=',
                    ],
                    [
                        'key'     => 'board_position.is_board', // The key check from your template.
                        'value'   => 1,
                    ],
                ],
            ];
            $board_terms = pods( 'board_term' )->find( $board_params );

            if ( $board_terms->total() > 0 ) {
                $position_title = $board_terms->field( 'board_position.post_title' );
                if ( ! empty( $position_title ) ) {
                    $activity_lines[] = '<strong>Board Position:</strong> ' . esc_html( $position_title );
                }
            }

            // Now, display the results.
            if ( empty( $activity_lines ) ) {
                echo '&mdash;';
            } else {
                echo implode( '<br>', $activity_lines );
            }
            break;
    }
}
add_action( 'manage_actor_posts_custom_column', 'tw_plays_render_actor_columns', 10, 2 );
