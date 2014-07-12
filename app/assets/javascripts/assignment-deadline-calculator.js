function dateAddDays(dateString, numberOfDays){
    var dateTemp = dateString.split('/').reverse().join('/'),
        newDate =  new Date(dateTemp);
    newDate.setDate(newDate.getDate()+numberOfDays||1);
    return [ zeroPad(newDate.getDate(),10)
        ,zeroPad(newDate.getMonth()+1,10)
        ,newDate.getFullYear() ].join('/');
}

function zeroPad(nr,base){
    var len = (String(base).length - String(nr).length)+1;
    return len > 0? new Array(len).join('0')+nr : nr;
}

$('#deadline_date').change(function() {
    var deadlineDate = $('#deadline_date').val();

    var markingStartDate = dateAddDays(deadlineDate, 1);
    var markingDeadline = dateAddDays(markingStartDate, 7);

//    alert('Marking start date: ' + markingStartDate);
//    alert('Marking deadline: ' + markingDeadline);

    $('#marking_start_date').val(markingStartDate).datepicker('update');
    $('#marking_deadline_date').val(markingDeadline).datepicker('update');
});