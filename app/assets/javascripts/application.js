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

// Javascript to enable link to tab
var url = document.location.toString();
if (url.match('#')) {
    $('.nav-tabs a[href=#'+url.split('#')[1]+']').tab('show') ;
}

// With HTML5 history API, we can easily prevent scrolling!
$('.nav-tabs a').on('shown.bs.tab', function (e) {
    if(history.pushState) {
        history.pushState(null, null, e.target.hash);
    } else {
        window.location.hash = e.target.hash; //Polyfill for old browsers
    }
})