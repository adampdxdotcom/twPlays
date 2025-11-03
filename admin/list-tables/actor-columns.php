<?php
/**
 * Custom List Table Columns for the 'Actor' Pod. (v2.6 - DEBUGGING VERSION)
 *
 * This version adds raw data output to the "Current Activity" column to help
 * diagnose why cast and crew roles are not appearing.
 *
 * @package TW_Plays
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ... (The CSS and column definition functions are unchanged and correct) ...

function tw_plays_actor_column_styles() {
    $current_screen = get_current_screen();
    if ( $current_screen && 'edit-actor' === $current_screen->id ) {
        echo '<style>.column-actor_headshot { width: 120px; }</style>';
    }
}
add_action( 'admin_head', 'tw_plays_actor_column_styles' );

function tw_plays_set_actor_columns( $columns ) {
    $new_columns = [ 'cb' => $columns['cb'] ];
    $new_columns['actor_headshot']         = 'Headshot';
    $new_columns['title']                  = 'Actor Name';
    $new_columns['actor_current_activity'] = 'Current Activity';
    $new_columns['date']                   = 'Date';
    return $new_columns;
}
add_filter( 'manage_actor_posts_columns', 'tw_plays_set_actor_columns' );

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
    // We will only run the debug on the first actor to keep the screen clean.
    static $debug_has_run = false;

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
            
            // =========================================================================
            // == START DEBUGGING BLOCK
            // =========================================================================
            if ( ! $debug_has_run ) {
                echo '<div style="background: #f1f1f1; border: 1px solid #ccc; padding: 10px; font-family: monospace; font-size: 12px; white-space: pre-wrap;">';

                // --- DEBUG 1: CASTING RECORDS ---
                echo "<strong>--- DEBUG: Casting Records for Actor ID: {$post_id} ---</strong><br>";
                $cast_params = [
                    'where' => [
                        [ 'key' => 'actor.ID', 'value' => $post_id ],
                        [
                            'key' => 'play.post_status', 'value' => 'publish', 'relation' => 'AND',
                            'where'    => [
                                [ 'key' => 'current_show', 'value' => 1 ],
                                [ 'key' => 'audition_status', 'value' => 1, 'relation' => 'OR' ],
                            ],
                        ],
                    ],
                ];
                $casting_records = pods( 'casting_record' )->find( $cast_params );
                echo "Total Found: " . $casting_records->total() . "<br>";
                // If records were found, print the data of the first one.
                if ( $casting_records->total() > 0 ) {
                    $casting_records->fetch();
                    echo "<strong>First Casting Record Data:</strong><br>";
                    print_r( $casting_records->data() );
                }
                echo "<hr>";

                // --- DEBUG 2: CREW RECORDS ---
                echo "<strong>--- DEBUG: Crew Records for Actor ID: {$post_id} ---</strong><br>";
                $crew_params = [
                    'where' => [
                        [ 'key' => 'actor.ID', 'value' => $post_id ],
                        [
                            'key' => 'play.post_status', 'value' => 'publish', 'relation' => 'AND',
                            'where'    => [
                                [ 'key' => 'current_show', 'value' => 1 ],
                                [ 'key' => 'audition_status', 'value' => 1, 'relation' => 'OR' ],
                            ],
                        ],
                    ],
                ];
                $crew_records = pods( 'crew' )->find( $crew_params );
                echo "Total Found: " . $crew_records->total() . "<br>";
                // If records were found, print the data of the first one.
                if ( $crew_records->total() > 0 ) {
                    $crew_records->fetch();
                    echo "<strong>First Crew Record Data:</strong><br>";
                    print_r( $crew_records->data() );
                }

                echo '</div>';
                $debug_has_run = true;
            }
            // =========================================================================
            // == END DEBUGGING BLOCK
            // =========================================================================

            // The original code is below, but the debug output will show above it.
            $activity_lines = [];
            // ... (original query code) ...
            if ( empty( $activity_lines ) ) {
                echo '&mdash;';
            } else {
                echo implode( '<br>', $activity_lines );
            }
            break;
    }
}
add_action( 'manage_actor_posts_custom_column', 'tw_plays_render_actor_columns', 10, 2 );
