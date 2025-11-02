// admin/assets/js/editor-scripts.js (FINAL AND CORRECTLY ORDERED)

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
       PLAY POD LIVE UPDATE 
       ========================================================================== */
    if ($('body').hasClass('post-type-play')) {
        // Wait for both the visible custom field AND the hidden title field to exist.
        waitForElement('#pods-form-ui-pods-field-play-name, #title', function() {
            var playNameField = $('#pods-form-ui-pods-field-play-name');
            var originalTitleInput = $('#title');

            function updatePlayTitleFromCustomField() {
                var newTitle = playNameField.val();
                if (typeof newTitle !== 'undefined') {
                    originalTitleInput.val(newTitle);
                }
            }
            
            if (playNameField.length > 0 && originalTitleInput.length > 0) {
                playNameField.on('keyup paste change', updatePlayTitleFromCustomField);
                updatePlayTitleFromCustomField(); // Sync on page load
            }
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
       SECTION 2: ACTOR POD LIVE UPDATE
       ========================================================================== */
    if ($('body').hasClass('post-type-actor')) {
        waitForElement('#pods-form-ui-pods-meta-actorname', function() {
            var actorNameField = $('#pods-form-ui-pods-meta-actorname');
            function updateActorTitle() {
                var newTitle = actorNameField.val();
                if (newTitle && typeof wp.data !== 'undefined') {
                    wp.data.dispatch('core/editor').editPost({ title: newTitle });
                }
            }
            if (actorNameField.length > 0) { actorNameField.on('keyup', updateActorTitle); }
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
