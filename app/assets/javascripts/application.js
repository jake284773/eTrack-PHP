/**
 * Clear modal when closed.
 *
 * This is necessary otherwise the next modal won't contain the correct content.
 */
$('body').on('hidden.bs.modal', '.modal', function () {
    $(this).removeData('bs.modal');
});