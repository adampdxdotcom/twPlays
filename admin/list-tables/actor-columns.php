<?php
/**
 * Custom List Table Columns for the 'Actor' Pod. (v4.1 - FATAL ERROR FIX)
 *
 * This version corrects a fatal PHP error caused by a typo in a function
 * definition. The core logic for querying and displaying data remains the same.
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
        echo '<style>.column-actor_headshot { width: 120px; }</style>';
    }
}
add_action( 'admin_head', 'tw_plays_actor_column_styles' );

/**
 * 2. REORDER & ADD Columns: Unchanged.
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
function tw_plays_set_actor_primary_column( $default, $screen_id ) { // CORRECTED a fatal typo here.
    if ( 'edit-actor' === $screen_id ) { // CORRECTED a fatal typo here.
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
            } else { echo '&mdash;'; }
            break;
        
        case 'actor_current_activity':
            $activity_lines = [];

            // --- Query 1 & 2: Cast & Crew (FINAL WORKING METHOD) ---
            $casting_records = pods( 'casting_record', [ 'where' => [ 'actor.ID' => $post_id ] ] );
            while ( $casting_records->fetch() ) {
                $play_id = $casting_records->field('play.ID');
                if ( !is_numeric($play_id) || $play_id <= 0 ) { continue; }
                $play_pod = pods('play', $play_id);
                if ( !$play_pod->exists() ) { continue; }
                
                if ( $play_pod->field('current_show') == 1 || $play_pod->field('audition_status') == 1 ) {
                    $play_title = $play_pod->field('post_title');
                    $character  = $casting_records->field('character_name');
                    $activity_lines[] = '<strong>Play:</strong> ' . esc_html( $play_title ) . ' as ' . esc_html( $character );
                }
            }

            $crew_records = pods( 'crew', [ 'where' => [ 'actor.ID' => $post_id ] ] );
            while ( $crew_records->fetch() ) {
                $play_id = $crew_records->field('play.ID');
                if ( !is_numeric($play_id) || $play_id <= 0 ) { continue; }
                $play_pod = pods('play', $play_id);
                if ( !$play_pod->exists() ) { continue; }
                
                if ( $play_pod->field('current_show') == 1 || $play_pod->field('audition_status') == 1 ) {
                    $play_title = $play_pod->field('post_title');
                    $position   = $crew_records->display('crew');
                    $activity_lines[] = '<strong>Play:</strong> ' . esc_html( $play_title ) . ', ' . esc_html( $position );
                }
            }
            
            // --- Query 3: Board Position (this method is already working) ---
            $board_terms = pods( 'board_term', [
                'limit' => 1,
                'where' => [
                    [ 'key' => 'board_member_name.ID', 'value' => $post_id ],
                    [ 'key' => 'start_date', 'value' => date('Y-m-d'), 'compare' => '<=', 'type' => 'DATE' ],
                    [ 'key' => 'end_date', 'value' => date('Y-m-d'), 'compare' => '>=', 'type' => 'DATE' ],
                ]
            ]);
            if ( $board_terms->total() > 0 ) {
                $board_terms->fetch();
                $position_id = $board_terms->field('board_position.ID');
                if ( is_numeric($position_id) && $position_id > 0 ) {
                    $position_pod = pods('positions', $position_id);
                    if ($position_pod && $position_pod->exists() && $position_pod->field('is_board') == 1) {
                        $position_title = $position_pod->field('post_title');
                        if (!empty($position_title)) {
                            $activity_lines[] = '<strong>Board Position:</strong> ' . esc_html($position_title);
                        }
                    }
                }
            }

            // Display all collected results.
            if ( empty( $activity_lines ) ) {
                echo '&mdash;';
            } else {
                echo implode( '<br>', $activity_lines );
            }
            break;
    }
}
add_action( 'manage_actor_posts_custom_column', 'tw_plays_render_actor_columns', 10, 2 );
