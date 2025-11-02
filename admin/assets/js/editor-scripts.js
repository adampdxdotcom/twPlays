// admin/assets/js/editor-scripts.js (FOCUSED DIAGNOSTIC VERSION)

jQuery(document).ready(function($) {

    // Announce that the script has loaded successfully.
    console.log('[TW PLAYS DEBUG] SUCCESS: editor-scripts.js has loaded and jQuery is ready.');

    // Only proceed if we are on the 'play' post type editor screen.
    if ($('body').hasClass('post-type-play')) {
        
        console.log('[TW PLAYS DEBUG] INFO: Play post type detected. Starting title sync test.');

        // Find the two title input fields using their unique IDs.
        var customTitleInput = $('#tw-plays-post-title');
        var originalTitleInput = $('#title');

        // Report what was found.
        console.log('[TW PLAYS DEBUG] CHECK: Searching for custom title input (#tw-plays-post-title). Found:', customTitleInput.length, 'element(s).');
        console.log('[TW PLAYS DEBUG] CHECK: Searching for original title input (#title). Found:', originalTitleInput.length, 'element(s).');

        // Proceed only if BOTH fields were successfully found on the page.
        if (customTitleInput.length > 0 && originalTitleInput.length > 0) {
            
            console.log('[TW PLAYS DEBUG] SUCCESS: Both title fields found. Attaching sync handler.');

            // This is the function that copies the text from our field to the real one.
            function syncTitles() {
                var newTitleValue = customTitleInput.val();
                originalTitleInput.val(newTitleValue);
                // Report the action in the console every time it runs.
                console.log('[TW PLAYS DEBUG] SYNCING: Copied "' + newTitleValue + '" to the original title field.');
            }

            // Set up the event listener to run the sync function whenever the user types.
            customTitleInput.on('keyup paste change', syncTitles);
            
            // Run the function once immediately when the page loads to handle existing titles.
            syncTitles();
            console.log('[TW PLAYS DEBUG] INFO: Initial sync complete on page load.');

        } else {
            // If one or both fields were not found, report a critical error.
            console.error('[TW PLAYS DEBUG] ERROR: Could not find one or both of the required title fields. The "Publish" button will not work.');
        }

    } else {
        // If we are not on a 'play' editor screen, do nothing.
        console.log('[TW PLAYS DEBUG] INFO: Not on the Play editor screen. Skipping title sync test.');
    }
});
