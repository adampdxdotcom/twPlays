// admin/assets/js/editor-scripts.js (MODIFIED FOR ACTOR POD)

jQuery(document).ready(function($) {

    /**
     * A robust function that waits for a specific element to appear in the DOM.
     * @param {string} selector - The CSS selector for the element to wait for.
     * @param {function} callback - The function to execute once the element is found.
     */
    function waitForElement(selector, callback) {
        var interval = setInterval(function() {
            if ($(selector).length > 0) {
                clearInterval(interval);
                callback();
            }
        }, 100); // Check every 100 milliseconds
    }

    /* ==========================================================================
       PLAY POD LIVE UPDATE (CLASSIC EDITOR METHOD)
       ========================================================================== */
    if ($('body').hasClass('post-type-play')) {
        // Wait for our custom input AND the original #title input to exist.
        waitForElement('#tw-plays-custom-title-input, #title', function() {

            var customTitleInput = $('#tw-plays-custom-title-input');
            var realTitleInput   = $('#title'); // This is the real, hidden WordPress title field.

            // This function copies the text from our custom field to the real one.
            function syncTitles() {
                var newTitle = customTitleInput.val();
                realTitleInput.val(newTitle);
            }

            // Run the sync function whenever the user types or pastes.
            customTitleInput.on('keyup paste change', syncTitles);

            // Sync on page load in case we are editing an existing post.
            syncTitles();
        });
    }
    
    /* ==========================================================================
       ACTOR POD LIVE UPDATE (CLASSIC EDITOR METHOD) - THIS IS THE MODIFIED SECTION
       ========================================================================== */
    if ($('body').hasClass('post-type-actor')) {
        // This logic is now an exact copy of the 'play' pod's logic.
        // It looks for the same custom title input we created in actor-editor.php.
        waitForElement('#tw-plays-custom-title-input, #title', function() {

            var customTitleInput = $('#tw-plays-custom-title-input');
            var realTitleInput   = $('#title'); // The real, hidden WordPress title field.

            function syncTitles() {
                var newTitle = customTitleInput.val();
                realTitleInput.val(newTitle);
            }

            customTitleInput.on('keyup paste change', syncTitles);
            syncTitles(); // Sync on page load
        });
    }

    /* ==========================================================================
       SECTION 1: CREW POD LIVE UPDATE
       ========================================================================== */
    if ($('body').hasClass('post-type-crew')) {
        waitForElement('[name="pods_meta_crew"]', function() {
            var crewField = $('[name="pods_meta_crew"]');
            var playContainer = $('.pods-form-ui-row-name-play');
            function updateCrewTitle() {
                var crewValue = crewField.find('option:selected').text().trim();
                var playValue = playContainer.find('.pods-dfv-pick-full-select__single-value').text().trim();
                if (crewField.val() === '') crewValue = '';
                var titleParts = [];
                if (crewValue) titleParts.push(crewValue);
                if (playValue) titleParts.push(playValue);
                var newTitle = titleParts.join(' - ');
                if (newTitle && typeof wp.data !== 'undefined') {
                    wp.data.dispatch('core/editor').editPost({ title: newTitle });
                }
            }
            if (crewField.length > 0) { crewField.on('change', updateCrewTitle); }
            if (playContainer.length > 0) { new MutationObserver(updateCrewTitle).observe(playContainer[0], { childList: true, subtree: true }); }
        });
    }

    /* ==========================================================================
       SECTION 3: CASTING RECORD POD LIVE UPDATE
       ========================================================================== */
    if ($('body').hasClass('post-type-casting_record')) {
        waitForElement('#pods-form-ui-pods-meta-character-name', function() {
            var characterNameField = $('#pods-form-ui-pods-meta-character-name');
            function updateCastingTitle() {
                var newTitle = characterNameField.val();
                if (newTitle && typeof wp.data !== 'undefined') {
                    wp.data.dispatch('core/editor').editPost({ title: newTitle });
                }
            }
            if (characterNameField.length > 0) { characterNameField.on('keyup', updateCastingTitle); }
        });
    }

    /* ==========================================================================
       SECTION 4: BOARD TERM POD LIVE UPDATE (FINAL VERSION)
       ========================================================================== */
    if ($('body').hasClass('post-type-board_term')) {
        waitForElement('#pods-meta-more-fields', function() {
            var fieldsContainer = $('#pods-meta-more-fields');
            var positionField = $('#pods-form-ui-pods-meta-board-position');
            var startDateField = $('#pods-form-ui-pods-meta-start-date');
            var endDateField = $('#pods-form-ui-pods-meta-end-date');
            function updateBoardTermTitle() {
                var positionValue = positionField.find('option:selected').text();
                var startDateValue = startDateField.val();
                var endDateValue = endDateField.val();
                if (positionField.val() && startDateValue && endDateValue) {
                    var startYear = startDateValue.split('-')[0];
                    var endYear = endDateValue.split('-')[0];
                    var newTitle = positionValue + ' ' + startYear + '-' + endYear;
                    if (typeof wp.data !== 'undefined') {
                        wp.data.dispatch('core/editor').editPost({ title: newTitle });
                    }
                }
            }
            if (fieldsContainer.length > 0) { new MutationObserver(updateBoardTermTitle).observe(fieldsContainer[0], { childList: true, subtree: true, attributes: true, characterData: true }); }
        });
    }

    /* ==========================================================================
       SECTION 5: LOCATION POD LIVE UPDATE (BLOCK EDITOR COMPATIBLE)
       ========================================================================== */
    if ($('body').hasClass('post-type-location')) {
        waitForElement('input[name="pods_meta_location_name"]', function() {
            var locationFieldSelector = 'input[name="pods_meta_location_name"]';
            $(document.body).on('keyup change paste', locationFieldSelector, function() {
                var newTitle = $(this).val();
                if (newTitle && typeof wp.data !== 'undefined') {
                    wp.data.dispatch('core/editor').editPost({ title: newTitle });
                }
            });
        });
    }
    
    /* ==========================================================================
       SECTION 6: EVENT POD LIVE UPDATE (NEWLY ADDED)
       ========================================================================== */
    if ($('body').hasClass('post-type-event')) {
        waitForElement('input[name="pods_meta_event_name"]', function() {
            var eventNameFieldSelector = 'input[name="pods_meta_event_name"]';
            $(document.body).on('keyup change paste', eventNameFieldSelector, function() {
                var newTitle = $(this).val();
                if (newTitle && typeof wp.data !== 'undefined') {
                    wp.data.dispatch('core/editor').editPost({ title: newTitle });
                }
            });
        });
    }

});
