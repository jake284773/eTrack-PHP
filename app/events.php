<?php

use eTrack\Accounts\User;

Event::listen('auth.login', function (User $user) {
    $user->last_login = new DateTime;

    $user->save();
});

Event::listen('tracker.calcAllFinalGrades',
    'eTrack\GradeCalculators\CalcEventHandler@onAllFinalGrades');

Event::listen('tracker.calcAllPredictedGrades',
    'eTrack\GradeCalculators\CalcEventHandler@onAllPredictedGrades');

Event::listen('tracker.calcFinalGradeStudent',
    'eTrack\GradeCalculators\CalcEventHandler@onFinalGradeStudent');

Event::listen('tracker.calcPredictedGradeStudent',
    'eTrack\GradeCalculators\CalcEventHandler@onPredictedGradeStudent');

Event::listen('tracker.calcUnitGradesCourse',
    'eTrack\GradeCalculators\CalcEventHandler@onUnitGradesCourse');

Event::listen('tracker.calcUnitGradesStudent',
    'eTrack\GradeCalculators\CalcEventHandler@onUnitGradesStudent');

Event::listen('tracker.calcUnitGradeStudent',
    'eTrack\GradeCalculators\CalcEventHandler@onUnitGradeStudent');