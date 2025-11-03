<?php
/**
 * CORRECTED DEBUGGING SCRIPT for Actor Columns.
 *
 * This version restores the column-creation functions so the page renders
 * correctly, and then adds debugging output to the 'Current Activity' column
 * for every actor to definitively show the data structure.
 *
 * @package TW_Plays
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * 1. ADD CSS to adjust the column widths for a cleaner look. (Restored)
 */
function tw_plays_actor_column_styles() {
    $current_screen = get_current_screen();
    if ( $current_screen && 'edit-actor' === $current_screen->id ) {
        echo '<style>.column-actor_headshot { width: 120px; }</style>';
    }
}
add_action( 'admin_head', 'tw_plays_actor_column_styles' );

/**
 * 2. REORDER & ADD Columns: This is essential for the debug hook to run. (Restored)
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
 * 3. DESIGNATE PRIMARY COLUMN: Unchanged. (Restored)
 */
function tw_plays_set_actor_primary_column( $default, $screen_id ) {
    if ( 'edit-actor' === $screen_id ) {
        return 'title';
    }
    return $default;
}
add_filter( 'list_table_primary_column', 'tw_plays_set_actor_primary_column', 10, 2 );


/**
 * 4. RENDER CONTENT and our DEBUGGING OUTPUT.
 */
function tw_plays_render_actor_columns( $column_name, $post_id ) {
    switch ( $column_name ) {
        case 'actor_headshot':
            $headshot_data = pods( 'actor', $post_id )->field( 'headshot' );
            if ( ! empty( $headshot_data['guid'] ) ) {
                $image_url = wp_get_attachment_image_url( $headshot_data['ID'], 'thumbnail' );
                echo '<img src="' . esc_url( $image_url ) . '" alt="Actor Headshot" style="height: 100px; width: 100px; object-fit: cover; border-radius: 4px;"/>';
            } else { echo '&mdash;'; }
            break;
        
        case 'actor_current_activity':
            
            // =========================================================================
            // == START DEBUGGING BLOCK
            // =========================================================================
            echo '<pre style="background: #fffbcf; color: #3c434a; border: 1px solid #f0e69a; padding: 10px; font-size: 12px; white-space: pre-wrap;">';

            // --- DUMP CASTING RECORDS ---
            echo "<strong>CASTING RECORDS (Actor ID: {$post_id})</strong>\n";
            $casting_records = pods( 'casting_record', [ 'where' => [ 'actor.ID' => $post_id ] ] );
            echo "Found: " . $casting_records->total() . "\n";
            if ( $casting_records->total() > 0 ) {
                foreach ( $casting_records as $record ) {
                    echo " -- Record ID: " . $record->id() . " --\n";
                    print_r( $record->data() );
                }
            }

            // --- DUMP CREW RECORDS ---
            echo "\n<strong>CREW RECORDS (Actor ID: {$post_id})</strong>\n";
            $crew_records = pods( 'crew', [ 'where' => [ 'actor.ID' => $post_id ] ] );
            echo "Found: " . $crew_records->total() . "\n";
            if ( $crew_records->total() > 0 ) {
                foreach ( $crew_records as $record ) {
                    echo " -- Record ID: " . $record->id() . " --\n";
                    print_r( $record->data() );
                }
            }
            
            echo '</pre>';
            // =========================================================================
            // == END DEBUGGING BLOCK
            // =========================================================================
            
            break;
    }
}
add_action( 'manage_actor_posts_custom_column', 'tw_plays_render_actor_columns', 10, 2 );
