<?php
/**
 * Custom List Table Columns for the 'Actor' Pod. (v3.2 - BULLETPROOF VERSION)
 *
 * This version adds stronger validation to prevent fatal errors when relationship
 * fields (like the link to a play) are empty or corrupted in the database.
 * This resolves the "Call to a member function field() on false" error permanently.
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

            // --- Query 1 & 2: Cast & Crew (with stronger sanity checks) ---
            $casting_records = pods( 'casting_record', [ 'where' => [ 'actor.ID' => $post_id ] ] );
            foreach ( $casting_records as $record ) {
                // --- STRONGER FIX (Part 1) ---
                $play_id = $record->field( 'play.ID' );
                if ( ! is_numeric( $play_id ) || $play_id <= 0 ) { continue; } // Ensures we have a valid post ID.
                $play_pod = pods( 'play', $play_id );
                if ( ! $play_pod || ! $play_pod->exists() ) { continue; }
                // --- END FIX ---

                if ( $play_pod->field('current_show') == 1 || $play_pod->field('audition_status') == 1 ) {
                    $play_title = $play_pod->field('post_title');
                    $character  = $record->field('character_name');
                    $activity_lines[] = '<strong>Play:</strong> ' . esc_html( $play_title ) . ' as ' . esc_html( $character );
                }
            }

            $crew_records = pods( 'crew', [ 'where' => [ 'actor.ID' => $post_id ] ] );
            foreach ( $crew_records as $record ) {
                // --- STRONGER FIX (Part 2) ---
                $play_id = $record->field( 'play.ID' );
                if ( ! is_numeric( $play_id ) || $play_id <= 0 ) { continue; } // This is the line that will fix the crash.
                $play_pod = pods( 'play', $play_id );
                if ( ! $play_pod || ! $play_pod->exists() ) { continue; }
                // --- END FIX ---

                if ( $play_pod->field('current_show') == 1 || $play_pod->field('audition_status') == 1 ) {
                    $play_title = $play_pod->field('post_title');
                    $position   = $record->display('crew');
                    $activity_lines[] = '<strong>Play:</strong> ' . esc_html( $play_title ) . ', ' . esc_html( $position );
                }
            }
            
            // --- Query 3: Board Position (this method is already bulletproof) ---
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
                        $activity_lines[] = '<strong>Board Position:</strong> ' . esc_html($position_title);
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
