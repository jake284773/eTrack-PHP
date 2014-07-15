<?php namespace eTrack\Controllers\Admin;

use App;
use DB;
use eTrack\Accounts\UserRepository;
use eTrack\Controllers\BaseController;
use eTrack\Courses\Course;
use eTrack\Courses\CourseRepository;
use Event;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Input;
use Redirect;
use Request;
use Validator;
use View;

class CourseStudentController extends BaseController {

    protected $courseRepository;
    protected $userRepository;

    public function __construct(CourseRepository $courseRepository,
                                UserRepository $userRepository)
    {
        $this->courseRepository = $courseRepository;
        $this->userRepository = $userRepository;
    }

    public function add($courseId)
    {
        try {
            $course = $this->courseRepository->find($courseId);
        } catch (ModelNotFoundException $e) {
            App::abort(404);
            return false;
        }

        $students = $this->userRepository->getStudentsNotEnrolledOnCourse($course);

        $studentsSelect = ['' => ''];

        foreach ($students as $student)
        {
            $studentsSelect[$student->id] = $student->full_name . ' (' . $student->id . ')';
        }

        $possibleGradesSelect = $this->getCourseGradesArray($course, true);

        return View::make('admin.courses.students.add', [
            'course' => $course,
            'students' => $studentsSelect,
            'possibleGrades' => $possibleGradesSelect
        ]);
    }

    public function store($courseId)
    {
        try {
            $course = $this->courseRepository->find($courseId);
        } catch (ModelNotFoundException $e) {
            App::abort(404);
            return false;
        }

        $possibleGradesList = implode(',', $this->getCourseGradesArray($course));

        $validationRules = [
            'student' => 'required|exists:user,id,role,Student|unique:course_student,student_user_id,NULL,id,course_id,'.$course->id,
            'target_grade' => 'in:'.$possibleGradesList,
        ];

        $formData = [
            'student' => Input::get('student'),
            'target_grade' => Input::get('target_grade'),
        ];

        $validation = Validator::make($formData, $validationRules);

        if ($validation->fails()) {
            return Redirect::back()->withInput()->withErrors($validation->errors());
        }

        try {
            DB::transaction(function() use($course, $formData, $courseId) {
                $course->students()->attach($formData['student'], ['target_grade' => $formData['target_grade']]);
                Event::fire('tracker.calcUnitGradesStudent', [$courseId, $formData['student']]);
                Event::fire('tracker.calcFinalGradeStudent', [$courseId, $formData['student']]);
                Event::fire('tracker.calcPredictedGradeStudent', [$courseId, $formData['student']]);
            });
        } catch (\Exception $e) {
            return Redirect::back()->withInput()->with('errorMessage', 'Unable add student to course.');
        }

        return Redirect::route('admin.courses.show', [$course->id, '#students'])->with('successMessage', 'Added student to course.');
    }

    public function edit($courseId, $studentId)
    {
        try {
            $course = $this->courseRepository->find($courseId);
            $student = $course->students()->where('student_user_id', '=', $studentId)
                ->withPivot('target_grade')->firstOrFail();
        } catch (ModelNotFoundException $e) {
            App::abort(404);
            return false;
        }

        $possibleGradesSelect = $this->getCourseGradesArray($course, true);

        if (Request::ajax()) {
            return View::make('admin.courses.students.adjust-target-grade', [
                'course' => $course,
                'student' => $student,
                'possibleGrades' => $possibleGradesSelect
            ]);
        } else {
            return;
        }
    }

    public function update($courseId, $studentId)
    {
        try {
            $course = $this->courseRepository->find($courseId);
            $student = $course->students()->where('student_user_id', '=', $studentId)
                ->withPivot('target_grade')->firstOrFail();
        } catch (ModelNotFoundException $e) {
            App::abort(404);
            return false;
        }

        $possibleGradesList = implode(',', $this->getCourseGradesArray($course));

        $validationRules = [
            'target_grade' => 'in:'.$possibleGradesList,
        ];

        $formData = [
            'target_grade' => Input::get('target_grade'),
        ];

        $validation = Validator::make($formData, $validationRules);

        if ($validation->fails()) {
            return Redirect::back()->withInput()->withErrors($validation->errors());
        }

        try {
            DB::transaction(function() use($course, $formData, $studentId) {
                $course->students()->updateExistingPivot(
                    $studentId,
                    ['target_grade' => $formData['target_grade']]
                );
            });
        } catch (\Exception $e) {
            return Redirect::back()->withInput()->with('errorMessage', 'Unable update target grade for student.');
        }

        return Redirect::route('admin.courses.show', [$course->id, '#students'])->with('successMessage', 'Updated target grade for student.');
    }

    public function deleteConfirm($courseId, $studentId)
    {
        try {
            $course = $this->courseRepository->find($courseId);
            $student = $course->students()->where('student_user_id', '=', $studentId)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            App::abort(404);
            return false;
        }

        if (Request::ajax()) {
            return View::make('admin.courses.students.delete.modal', ['course' => $course, 'student' => $student]);
        }

        return View::make('admin.courses.students.delete.fallback', ['course' => $course, 'student' => $student]);
    }

    public function destroy($courseId, $studentId)
    {
        try {
            $course = $this->courseRepository->find($courseId);
            $course->students()->where('student_user_id', '=', $studentId)->firstOrFail();
        } catch (ModelNotFoundException $e) {
            App::abort(404);
            return false;
        }

        try {
            DB::transaction(function() use($course, $studentId) {
                $course->students()->detach($studentId);
            });
        } catch (\Exception $e) {
            return Redirect::route($courseId, '#students')->with('errorMessage', 'Unable to remove student from course.');
        }

        return Redirect::route('admin.courses.show', [$courseId, '#students'])->with('successMessage', 'Removed student from course.');
    }

    /**
     * Retrieve an array of all the possible grades for a course.
     *
     * @param \eTrack\Courses\Course $course
     * @param bool $selectForm Whether the array should be formatted for a select form element
     * @param bool $excludeNya Whether or not to exclude the NYA grade
     * @return array
     */
    private function getCourseGradesArray(Course $course, $selectForm = false, $excludeNya = true)
    {
        if ($selectForm) {
            $possibleGradesSelect = ['' => ''];
        } else {
            $possibleGradesSelect = [];
        }

        foreach ($course->getPossibleGrades() as $possibleGrade) {
            $grade = $possibleGrade->getGrade();

            if ($excludeNya) {
                if ($grade != 'NYA') {
                    $possibleGradesSelect[$grade] = $grade;
                }
            } else {
                $possibleGradesSelect[$grade] = $grade;
            }
        }
        return $possibleGradesSelect;
    }

} 