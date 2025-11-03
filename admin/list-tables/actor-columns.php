<?php
/**
 * Custom List Table Columns for the 'Actor' Pod. (v2.8 - ULTIMATE DEBUGGING)
 *
 * This version dumps all raw data for the Actor pod itself to discover the
 * correct names of the relationship fields pointing to cast/crew records.
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
            // ... code unchanged ...
            break;
        
        case 'actor_current_activity':
            
            // =========================================================================
            // == START ULTIMATE DEBUGGING BLOCK
            // =========================================================================
            if ( ! $debug_has_run ) {
                echo '<div style="background: #fff; border: 2px solid #D54E21; padding: 15px; font-family: monospace; font-size: 12px; white-space: pre-wrap; margin-bottom: 20px;">';

                // --- DEBUG: DUMP ALL DATA FOR THIS ACTOR ---
                echo "<strong>--- DEBUG: ALL Raw Data for Actor ID: {$post_id} ---</strong><br><br>";
                
                // Load the actor pod for this specific actor.
                $actor_pod = pods( 'actor', $post_id );

                // Dump all the raw data it contains.
                print_r( $actor_pod->data() );

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
