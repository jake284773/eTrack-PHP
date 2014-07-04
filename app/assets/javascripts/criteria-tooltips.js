$(function () {
    $("td.student").tooltip({container: 'body'});

    $("td.grade-nya").tooltip({
        placement: 'bottom',
        title: 'Not yet achieved',
        container: 'body'
    });

    $("td.grade-p").tooltip({
        placement: 'bottom',
        title: 'Pass',
        container: 'body'
    });

    $("td.grade-pp").tooltip({
        placement: 'bottom',
        title: 'Pass Pass',
        container: 'body'
    });

    $("td.grade-ppp").tooltip({
        placement: 'bottom',
        title: 'Pass Pass Pass',
        container: 'body'
    });

    $("td.grade-m").tooltip({
        placement: 'bottom',
        title: 'Merit',
        container: 'body'
    });

    $("td.grade-mm").tooltip({
        placement: 'bottom',
        title: 'Merit Merit',
        container: 'body'
    });

    $("td.grade-mmm").tooltip({
        placement: 'bottom',
        title: 'Merit Merit Merit',
        container: 'body'
    });

    $("td.grade-d").tooltip({
        placement: 'bottom',
        title: 'Distinction',
        container: 'body'
    });

    $("td.grade-dd").tooltip({
        placement: 'bottom',
        title: 'Distinction Distinction',
        container: 'body'
    });

    $("td.grade-ddd").tooltip({
        placement: 'bottom',
        title: 'Distinction Distinction Distinction',
        container: 'body'
    });

    $('td.grade-d\\*').tooltip({
        placement: 'bottom',
        title: 'Distinction*',
        container: 'body'
    });

    $('td.grade-d\\*d\\*').tooltip({
        placement: 'bottom',
        title: 'Distinction* Distinction*',
        container: 'body'
    });

    $('td.grade-d\\*d\\*d\\*').tooltip({
        placement: 'bottom',
        title: 'Distinction* Distinction* Distinction*',
        container: 'body'
    });

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