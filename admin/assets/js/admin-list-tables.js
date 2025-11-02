/**
 * JavaScript for making the custom list table columns interactive.
 *
 * This script handles:
 * 1. The AJAX requests for the yes/no toggle switches.
 * 2. Applying dynamic row colors based on Pods field data.
 *
 * @package TW_Plays
 */

jQuery(document).ready(function($) {

    // --- 1. Dynamic Row Coloring Handler ---
    // This runs once as soon as the page is ready.
    
    // Find all table rows for the 'play' post type that have our custom color class.
    $('tr.post-type-play.has-custom-bg-color').each(function() {
        var $row = $(this);
        
        // The PHP filter adds our data as attributes that look like classes.
        // We need to parse them out of the class string.
        var classList = $row.attr('class').split(/\s+/);
        var bgColor = '';
        var textColor = '';

        $.each(classList, function(index, item) {
            if (item.startsWith('data-bg-color=')) {
                bgColor = item.split('=')[1].replace(/"/g, ''); // Get value and remove quotes
            }
            if (item.startsWith('data-text-color=')) {
                textColor = item.split('=')[1].replace(/"/g, ''); // Get value and remove quotes
            }
        });

        // Apply the background color if it was found.
        if (bgColor) {
            $row.css('background-color', bgColor);
        }

        // Apply the text color to the row and all links within it if found.
        if (textColor) {
            $row.css('color', textColor);
            $row.find('a').css('color', textColor);
            
            // Special case: Make the row actions (Edit, Trash) more visible on dark backgrounds
            $row.find('.row-actions a').css('color', textColor);
            $row.find('a.row-title').css('color', textColor);
        }
    });


    // --- 2. AJAX Toggle Switch Handler ---

    // Use event delegation for clicks on our toggle links.
    $('#posts-filter').on('click', '.tw-plays-toggle', function(e) {
        
        e.preventDefault();

        var $link = $(this);
        var postId = $link.data('post-id');
        var field = $link.data('field');
        var currentStatus = $link.data('current-status');
        var newStatus = (currentStatus === 1) ? 0 : 1;
        var $icon = $link.find('.dashicons');

        $link.addClass('is-loading');
        $icon.css('transform', 'rotate(360deg)');

        var data = {
            action: 'tw_plays_update_status',
            nonce: tw_plays_ajax.nonce,
            post_id: postId,
            field: field,
            new_status: newStatus
        };

        $.post(ajaxurl, data, function(response) {
            $link.removeClass('is-loading');
            $icon.css('transform', '');

            if (response.success) {
                $link.data('current-status', newStatus);
                if (newStatus === 1) {
                    $icon.removeClass('dashicons-dismiss').addClass('dashicons-yes-alt').css('color', 'green');
                } else {
                    $icon.removeClass('dashicons-yes-alt').addClass('dashicons-dismiss').css('color', '#a0a5aa');
                }
            } else {
                if (response.data && response.data.message) {
                    alert('Error: ' + response.data.message);
                } else {
                    alert('An unknown error occurred.');
                }
            }
        });
    });

    // Add basic CSS for our loading spinner effect.
    $('head').append('<style>.tw-plays-toggle.is-loading .dashicons { transition: transform 0.5s linear; }</style>');
});
