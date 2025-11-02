// admin/assets/js/editor-scripts.js

jQuery(document).ready(function($) {

    // =========================================================================
    // NEW FOR 'PLAY' POD: Sync our custom title field with the real one.
    // This runs immediately because it doesn't depend on the Block Editor.
    // =========================================================================
    if ($('body').hasClass('post-type-play')) {
        
        var customTitleInput = $('#tw-plays-post-title');
        var originalTitleInput = $('#title'); // The ID of the original WordPress title input

        // Ensure both fields exist before trying to do anything.
        if (customTitleInput.length && originalTitleInput.length) {
            
            function syncTitles() {
                originalTitleInput.val(customTitleInput.val());
            }

            // Sync whenever the user types, pastes, or changes the custom input.
            customTitleInput.on('keyup paste change', syncTitles);
            
            // Run once on page load to handle pre-filled titles when editing an existing play.
            syncTitles();
        }
    }


    // A delay for the Block Editor scripts to ensure components are loaded.
    // This is for all the other pods that still use the block editor.
    setTimeout(function() {

        /* ==========================================================================
           SECTION 1: CREW POD LIVE UPDATE
           ========================================================================== */
        if ($('body').hasClass('post-type-crew')) {
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
            if (crewField.length > 0) {
                crewField.on('change', updateCrewTitle);
            }
            if (playContainer.length > 0) {
                var observer = new MutationObserver(updateCrewTitle);
                observer.observe(playContainer[0], { childList: true, subtree: true });
            }
        }

        /* ==========================================================================
           SECTION 2: ACTOR POD LIVE UPDATE
           ========================================================================== */
        if ($('body').hasClass('post-type-actor')) {
            var actorNameField = $('#pods-form-ui-pods-meta-actorname');

            function updateActorTitle() {
                var newTitle = actorNameField.val();
                if (newTitle && typeof wp.data !== 'undefined') {
                    wp.data.dispatch('core/editor').editPost({ title: newTitle });
                }
            }
            if (actorNameField.length > 0) {
                actorNameField.on('keyup', updateActorTitle);
            } else {
                console.error('Could not find the actor name field with ID #pods-form-ui-pods-meta-actorname');
            }
        }

        /* ==========================================================================
           SECTION 3: CASTING RECORD POD LIVE UPDATE
           ========================================================================== */
        if ($('body').hasClass('post-type-casting_record')) {
            var characterNameField = $('#pods-form-ui-pods-meta-character-name');

            function updateCastingTitle() {
                var newTitle = characterNameField.val();
                if (newTitle && typeof wp.data !== 'undefined') {
                    wp.data.dispatch('core/editor').editPost({ title: newTitle });
                }
            }
            if (characterNameField.length > 0) {
                characterNameField.on('keyup', updateCastingTitle);
            } else {
                console.error('Could not find the character name field with ID #pods-form-ui-pods-meta-character-name');
            }
        }

        /* ==========================================================================
           SECTION 4: BOARD TERM POD LIVE UPDATE (FINAL VERSION)
           ========================================================================== */
        if ($('body').hasClass('post-type-board_term')) {
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
            
            if (fieldsContainer.length > 0) {
                var observer = new MutationObserver(updateBoardTermTitle);
                observer.observe(fieldsContainer[0], {
                    childList: true,
                    subtree: true,
                    attributes: true,
                    characterData: true
                });
            } else {
                console.error('Board Term Error: Could not find the main fields container #pods-meta-more-fields');
            }
        }

        /* ==========================================================================
           SECTION 5: LOCATION POD LIVE UPDATE (BLOCK EDITOR COMPATIBLE)
           ========================================================================== */
        if ($('body').hasClass('post-type-location')) {
            
            console.log('Location Pod script is running.'); // For debugging

            var locationFieldSelector = 'input[name="pods_meta_location_name"]';
            $(document.body).on('keyup change paste', locationFieldSelector, function() {
                
                var newTitle = $(this).val();

                if (newTitle && typeof wp.data !== 'undefined') {
                    wp.data.dispatch('core/editor').editPost({ title: newTitle });
                }
            });
        }
		
        /* ==========================================================================
           SECTION 6: EVENT POD LIVE UPDATE (NEWLY ADDED)
           ========================================================================== */
        if ($('body').hasClass('post-type-event')) {
            
            var eventNameFieldSelector = 'input[name="pods_meta_event_name"]';
            $(document.body).on('keyup change paste', eventNameFieldSelector, function() {
                
                var newTitle = $(this).val();

                if (newTitle && typeof wp.data !== 'undefined') {
                    wp.data.dispatch('core/editor').editPost({ title: newTitle });
                }
            });
        }

    }, 2000);
});
