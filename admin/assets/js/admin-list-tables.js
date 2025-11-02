/**
 * JavaScript for making the custom list table columns interactive.
 *
 * This script handles the AJAX requests for the yes/no toggle switches.
 *
 * @package TW_Plays
 */

// We wrap our code in a jQuery document ready function to ensure the DOM is loaded.
jQuery(document).ready(function($) {

    // --- AJAX Toggle Switch Handler ---

    // Find the container for the list table, as it will exist on page load.
    // We use event delegation to handle clicks, which is more efficient and works
    // even after "quick edit" or other DOM changes.
    $('#posts-filter').on('click', '.tw-plays-toggle', function(e) {
        
        // Prevent the default link behavior.
        e.preventDefault();

        // Get the clicked element and the data we stored in its attributes.
        var $link = $(this);
        var postId = $link.data('post-id');
        var field = $link.data('field');
        var currentStatus = $link.data('current-status');
        
        // Determine the new status (the opposite of the current one).
        var newStatus = (currentStatus === 1) ? 0 : 1;

        // Find the icon span inside the link.
        var $icon = $link.find('.dashicons');

        // Immediately give visual feedback by adding a "spinner" class.
        $link.addClass('is-loading');
        $icon.css('transform', 'rotate(360deg)'); // Simple spin effect.

        // Prepare the data to be sent to the server.
        var data = {
            action: 'tw_plays_update_status', // The name of our PHP AJAX action.
            nonce: tw_plays_ajax.nonce,       // The security token.
            post_id: postId,
            field: field,
            new_status: newStatus
        };

        // Send the AJAX request to the server.
        $.post(ajaxurl, data, function(response) {
            
            // Remove the loading feedback.
            $link.removeClass('is-loading');
            $icon.css('transform', ''); // Reset spin.

            if (response.success) {
                // --- Update was successful ---

                // Update the data attribute on the link.
                $link.data('current-status', newStatus);

                // Update the icon and its color based on the new status.
                if (newStatus === 1) {
                    $icon.removeClass('dashicons-dismiss').addClass('dashicons-yes-alt').css('color', 'green');
                } else {
                    $icon.removeClass('dashicons-yes-alt').addClass('dashicons-dismiss').css('color', '#a0a5aa');
                }
            } else {
                // --- Update failed ---
                
                // Optional: Show an error message.
                if (response.data && response.data.message) {
                    alert('Error: ' + response.data.message);
                } else {
                    alert('An unknown error occurred.');
                }
            }
        });
    });

    // Add some basic CSS for our loading spinner effect.
    $('head').append('<style>.tw-plays-toggle.is-loading .dashicons { transition: transform 0.5s linear; }</style>');
});
