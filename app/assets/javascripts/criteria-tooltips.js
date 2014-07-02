$(function () {
    $("td.criteria-achieved").tooltip({
        placement: 'bottom',
        title: 'Achieved',
        container: 'body'
    });

    $("td.criteria-awaitmark").tooltip({
        placement: 'bottom',
        title: 'Submitted, awaiting marking',
        container: 'body'
    });

    $("td.criteria-nya").tooltip({
        placement: 'bottom',
        title: 'Not yet achieved',
        container: 'body'
    });

    $("td.criteria-r1").tooltip({
        placement: 'bottom',
        title: 'Referral (first attempt)',
        container: 'body'
    });

    $("td.criteria-r2").tooltip({
        placement: 'bottom',
        title: 'Referral (second attempt)',
        container: 'body'
    });
});