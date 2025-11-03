<?php
/**
 * Custom Dashboard Page (v2.1 with Corrected Counts)
 *
 * This version corrects the queries for counting actors and crew members to ensure
 * the numbers are displayed correctly on the dashboard.
 *
 * @package TW_Plays
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * A helper function to render a featured play card on the dashboard.
 */
function tw_plays_render_dashboard_play_card( $play_pod, $title ) {
    ?>
    <div class="play-card <?php echo ( ! $play_pod->total() > 0 ) ? 'is-empty' : ''; ?>">
        <h3><?php echo esc_html( $title ); ?></h3>
        <?php if ( $play_pod->total() > 0 ) : ?>
            <?php
                $play_pod->fetch();
                $play_id = $play_pod->id();
                
                $poster_data = $play_pod->field('poster');
                $image_url = ! empty( $poster_data['guid'] ) ? wp_get_attachment_image_url( $poster_data['ID'], 'medium' ) : '';

                $start_date = $play_pod->field('start_date');
                $end_date   = $play_pod->field('end_date');
                $date_range = ( ! empty( $start_date ) && ! empty( $end_date ) ) ? date( 'M j', strtotime( $start_date ) ) . ' - ' . date( 'M j, Y', strtotime( $end_date ) ) : 'Dates not set';

                // --- THE FIX IS HERE ---
                // We use the reliable find()->total() method to get the counts.
                $actor_count = pods( 'casting_record' )->find( [ 'where' => 'play.ID = ' . $play_id ] )->total();
                $crew_count  = pods( 'crew' )->find( [ 'where' => 'play.ID = ' . $play_id ] )->total();
            ?>
            <div class="play-card-content">
                <?php if ( $image_url ) : ?>
                    <div class="play-card-poster">
                        <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $play_pod->field('post_title') ); ?> Poster">
                    </div>
                <?php endif; ?>
                <div class="play-card-details">
                    <h4><a href="<?php echo esc_url( get_edit_post_link( $play_id ) ); ?>"><?php echo esc_html( $play_pod->field('post_title') ); ?></a></h4>
                    <p class="play-dates"><?php echo esc_html( $date_range ); ?></p>
                    <div class="play-counts">
                        <span><strong>Actors:</strong> <?php echo esc_html( $actor_count ); ?></span>
                        <span><strong>Crew:</strong> <?php echo esc_html( $crew_count ); ?></span>
                    </div>
                </div>
            </div>
        <?php else : ?>
            <div class="play-card-empty">
                <p>No play is currently set as "<?php echo esc_html( str_replace( 'Next', 'Auditioning for', $title ) ); ?>".</p>
                <a href="<?php echo esc_url( admin_url('edit.php?post_type=play') ); ?>" class="button button-secondary">Select a Play</a>
            </div>
        <?php endif; ?>
    </div>
    <?php
}


/**
 * Renders the HTML content for the dashboard page.
 */
function tw_plays_render_dashboard_page() {
    // Security check.
    if ( ! current_user_can( 'edit_posts' ) ) {
        wp_die( __( 'You do not have sufficient permissions to access this page.', 'tw-plays' ) );
    }

    // Find our featured plays.
    $now_playing_pod = pods( 'play' )->find( [ 'limit' => 1, 'where' => 'current_show.meta_value = 1' ] );
    $next_up_pod = pods( 'play' )->find( [ 'limit' => 1, 'where' => 'audition_status.meta_value = 1' ] );

    $pods_to_display = [
        'play'           => [ 'label' => 'Plays', 'singular' => 'Play' ],
        'actor'          => [ 'label' => 'Actors', 'singular' => 'Actor' ],
        'crew'           => [ 'label' => 'Crew', 'singular' => 'Crew Member' ],
        'casting_record' => [ 'label' => 'Casting Records', 'singular' => 'Casting Record' ],
        'board_term'     => [ 'label' => 'Board Terms', 'singular' => 'Board Term' ],
        'positions'      => [ 'label' => 'Positions', 'singular' => 'Position' ],
    ];
    ?>
    
    <div class="wrap tw-plays-dashboard">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <p class="description">Welcome to the Theatre West management dashboard. Here you can see a summary of your site's data and quickly add new content.</p>
        
        <!-- Featured Plays Section -->
        <div class="dashboard-section featured-plays">
            <?php 
                tw_plays_render_dashboard_play_card( $now_playing_pod, 'Now Playing' );
                tw_plays_render_dashboard_play_card( $next_up_pod, 'Next Up' );
            ?>
        </div>


        <!-- At a Glance Statistics (Unchanged) -->
        <div class="dashboard-section">
            <h2>At a Glance</h2>
            <div class="stats-container">
                <?php foreach ( $pods_to_display as $pod_slug => $details ) : ?>
                    <?php
                        $post_count = wp_count_posts( $pod_slug )->publish;
                        $list_url = admin_url( 'edit.php?post_type=' . $pod_slug );
                    ?>
                    <div class="stat-box">
                        <a href="<?php echo esc_url( $list_url ); ?>">
                            <span class="stat-count"><?php echo esc_html( number_format_i18n( $post_count ) ); ?></span>
                            <span class="stat-label"><?php echo esc_html( $details['label'] ); ?></span>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Quick Links / Actions (Unchanged) -->
        <div class="dashboard-section">
            <h2>Quick Links</h2>
            <div class="quick-links-container">
                <?php foreach ( $pods_to_display as $pod_slug => $details ) : ?>
                    <?php
                        $add_new_url = admin_url( 'post-new.php?post_type=' . $pod_slug );
                    ?>
                    <a href="<?php echo esc_url( $add_new_url ); ?>" class="button button-primary">
                        + Add New <?php echo esc_html( $details['singular'] ); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        
    </div>

    <?php
}
