<?php
/**
 * Custom Dashboard Page
 *
 * Renders the content for the main "TW Plays" dashboard page,
 * including at-a-glance stats and quick links.
 *
 * @package TW_Plays
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders the HTML content for the dashboard page.
 */
function tw_plays_render_dashboard_page() {
    // Security check: ensure the user has the required capability.
    if ( ! current_user_can( 'edit_posts' ) ) {
        wp_die( __( 'You do not have sufficient permissions to access this page.', 'tw-plays' ) );
    }

    // Define the Pods we want to display statistics for.
    // 'label' is for the stat box, 'singular' is for the "Add New" button.
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

        <!-- Section 1: At a Glance Statistics -->
        <div class="dashboard-section">
            <h2>At a Glance</h2>
            <div class="stats-container">
                <?php foreach ( $pods_to_display as $pod_slug => $details ) : ?>
                    <?php
                        // Get the count of published posts for the current Pod.
                        $post_count = wp_count_posts( $pod_slug )->publish;
                        // The URL to the list table for this Pod.
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

        <!-- Section 2: Quick Links / Actions -->
        <div class="dashboard-section">
            <h2>Quick Links</h2>
            <div class="quick-links-container">
                <?php foreach ( $pods_to_display as $pod_slug => $details ) : ?>
                    <?php
                        // The URL to the "Add New" page for this Pod.
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
