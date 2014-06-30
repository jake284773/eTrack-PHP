/**
 * Clear modal when closed.
 *
 * This is necessary otherwise the next modal won't contain the correct content.
 */
$('body').on('hidden.bs.modal', '.modal', function () {
    $(this).removeData('bs.modal');
});

/**
 * Unit creation validation
 */
$("#unit_id").on('keyup paste', function () {
    oldtxt = $(this).val();
    find = '(\//?)(\ ?)';
    newtxt = oldtxt.replace(new RegExp(find, 'g'), '-');
    $(this).val(newtxt);
});

$('#unit_number').keyup(function () {
    this.value = this.value.replace(/[^0-9\.]/g,'');
});

$('#credit_value').keyup(function () {
    this.value = this.value.replace(/[^0-9\.]/g,'');
});

$('#glh').keyup(function () {
    this.value = this.value.replace(/[^0-9\.]/g,'');
});