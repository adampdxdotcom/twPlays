<?php
/**
 * Custom List Table Columns for the 'Actor' Pod. (v2.7 - SIMPLER DEBUGGING)
 *
 * This version simplifies the queries to find ANY cast/crew record for an actor,
 * ignoring the play's 'current_show' status, to isolate the problem.
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
    static $debug_has_run = false;

    switch ( $column_name ) {

        case 'actor_headshot':
            // ... (code unchanged)
            break;
        
        case 'actor_current_activity':
            
            // =========================================================================
            // == START SIMPLER DEBUGGING BLOCK
            // =========================================================================
            if ( ! $debug_has_run ) {
                echo '<div style="background: #f1f1f1; border: 1px solid #ccc; padding: 10px; font-family: monospace; font-size: 12px; white-space: pre-wrap;">';

                // --- DEBUG 1: ANY CASTING RECORDS ---
                echo "<strong>--- DEBUG (Simpler): ANY Casting Records for Actor ID: {$post_id} ---</strong><br>";
                $cast_params = [
                    'where' => [
                        // We are ONLY checking the link to the actor.
                        [ 'key' => 'actor.ID', 'value' => $post_id ],
                    ],
                ];
                $casting_records = pods( 'casting_record' )->find( $cast_params );
                echo "Total Found: " . $casting_records->total() . "<br>";
                if ( $casting_records->total() > 0 ) {
                    $casting_records->fetch();
                    echo "<strong>First Casting Record Data:</strong><br>";
                    print_r( $casting_records->data() );
                }
                echo "<hr>";

                // --- DEBUG 2: ANY CREW RECORDS ---
                echo "<strong>--- DEBUG (Simpler): ANY Crew Records for Actor ID: {$post_id} ---</strong><br>";
                $crew_params = [
                    'where' => [
                        // We are ONLY checking the link to the actor.
                        [ 'key' => 'actor.ID', 'value' => $post_id ],
                    ],
                ];
                $crew_records = pods( 'crew' )->find( $crew_params );
                echo "Total Found: " . $crew_records->total() . "<br>";
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

            echo '&mdash;'; // We only show the debug output for now.
            break;
    }
}
add_action( 'manage_actor_posts_custom_column', 'tw_plays_render_actor_columns', 10, 2 );
