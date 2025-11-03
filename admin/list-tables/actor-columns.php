<?php
/**
 * ULTIMATE DEBUGGING SCRIPT for Actor Columns.
 *
 * This script will halt execution and dump all raw data for a specific actor's
 * roles. This will definitively show the correct data structure.
 *
 * @package TW_Plays
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// We only need one function for this test.
function tw_plays_ultimate_actor_debug( $column_name, $post_id ) {
    // ====> IMPORTANT: REPLACE THIS WITH THE ID OF AN ACTOR YOU KNOW HAS A ROLE <====
    $target_actor_id = 5409; 

    // Only run this test on our specific target actor.
    if ( $column_name === 'actor_current_activity' && $post_id === $target_actor_id ) {
        
        echo '<pre style="background: #1d2327; color: #fff; padding: 20px; font-size: 14px; white-space: pre-wrap; margin: 20px;">';

        // --- DUMP CASTING RECORDS ---
        echo "<h1>--- DEBUGGING CASTING RECORDS ---</h1>";
        $casting_records = pods( 'casting_record', [ 'where' => [ 'actor.ID' => $target_actor_id ] ] );
        echo "<h2>Found: " . $casting_records->total() . " Casting Records</h2>";

        if ( $casting_records->total() > 0 ) {
            echo "<h3>Looping through records...</h3>";
            foreach ( $casting_records as $record ) {
                echo "\n--- Casting Record ID: " . $record->id() . " ---\n";
                var_dump( $record->data() );
            }
        }

        // --- DUMP CREW RECORDS ---
        echo "\n\n<h1>--- DEBUGGING CREW RECORDS ---</h1>";
        $crew_records = pods( 'crew', [ 'where' => [ 'actor.ID' => $target_actor_id ] ] );
        echo "<h2>Found: " . $crew_records->total() . " Crew Records</h2>";
        
        if ( $crew_records->total() > 0 ) {
            echo "<h3>Looping through records...</h3>";
            foreach ( $crew_records as $record ) {
                echo "\n--- Crew Record ID: " . $record->id() . " ---\n";
                var_dump( $record->data() );
            }
        }
        
        echo '</pre>';
        
        // Stop everything so we only see this output.
        die( 'DEBUGGING COMPLETE. Please copy the text from the black box above.' );
    }
}
// We hook into the action to run our test.
add_action( 'manage_actor_posts_custom_column', 'tw_plays_ultimate_actor_debug', 10, 2 );
