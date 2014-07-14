<?php namespace eTrack\Accounts;

use DB;
use eTrack\Core\EloquentRepository;
use Symfony\Component\Process\Exception\InvalidArgumentException;

/**
 * Higher abstraction for interacting with the user table in the database.
 *
 * Mainly contains functionality on searching and filtering as the common methods
 * are inherited from the EloquentRepository class.
 *
 * @package eTrack\Accounts
 */
class UserRepository extends EloquentRepository {

    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * Produce a query for filtering the users table based on the specified role.
     *
     * @param string $role The user role to filter.
     * @return \Illuminate\Database\Eloquent\Builder|static
     * @throws \Symfony\Component\Process\Exception\InvalidArgumentException
     */
    public function queryByRole($role)
    {
        if (! in_array($role, $this->model->validRoles())) {
            throw new InvalidArgumentException;
        }

        return $this->model->where('role', '=', $role);
    }

    public function getAllTutors()
    {
        return $this->model->where('role', '=', 'Tutor')
            ->orWhere('role', '=', 'Course Organiser')
            ->get();
    }

    public function getStudentsNotEnrolledOnCourse($courseId)
    {
        // Retrieve an array list of all the student IDs that are already enrolled
        // on the course.
        $studentIdsAlreadyEnrolled = DB::table('course_student')
            ->where('course_student.course_id', '=', $courseId)
            ->lists('student_user_id');

        $query = $this->model
            ->where('role', '=', 'Student')
            ->orderBy(DB::raw("substring_index(full_name, ' ', -1)"));

        // If there are any enrolled students in any of the groups for this
        // course, then exclude them from the result.
        if ($studentIdsAlreadyEnrolled) {
            $query = $query->whereNotIn('id', $studentIdsAlreadyEnrolled);
        }

        return $query->get();
    }

    /**
     * Return all the user records that match the specified role
     *
     * @param string $role The user role to match.
     * @return mixed
     */
    public function getByRole($role)
    {
        return $this->queryByRole($role)->get();
    }

    /**
     * Retrieve a paginator object for a paginated list of all the user records
     * that match the specified user role.
     *
     * @param string $role The user role to filter by
     * @param int $count The number of records per page
     * @return \Illuminate\Pagination\Paginator
     */
    public function getPaginatedByRole($role, $count = 15)
    {
        return $this->queryByRole($role)->paginate($count);
    }

    /**
     * Produce a query that selects records that match the specified search term.
     *
     * The id, full_name and email fields are used for searching.
     *
     * @param string $search The search term to search with.
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public function queryBySearch($search)
    {
        $searchString = '%'.$search.'%';

        return $this->model
            ->where('id', 'LIKE', $searchString)
            ->orWhere('full_name', 'LIKE', $searchString)
            ->orWhere('email', 'LIKE', $searchString);
    }

    /**
     * Retrieve all records that match the specified search term.
     *
     * @param string $search The search term to search with
     * @return mixed
     */
    public function getBySearch($search)
    {
        return $this->queryBySearch($search)->all();
    }

    /**
     * Retrieve a paginated list of all the user records that match the specified
     * user role.
     *
     * @param string $search The search term to search with
     * @param int $count The number of records per page
     * @return \Illuminate\Pagination\Paginator
     */
    public function getPaginatedBySearch($search, $count = 15)
    {
        return $this->queryBySearch($search)->paginate($count);
    }

    /**
     * Produce a query that filters the user table by the specified role
     * and searches for records that match the specified search term.
     *
     * The id, full_name and email fields are used for searching.
     *
     * @param string $role The user role to filter by
     * @param string $search The search term to search with
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    protected function queryByRoleAndSearch($role, $search)
    {
        $searchString = '%'.$search.'%';

        return $this->queryByRole($role)
            ->where(function($query) use($searchString)
            {
                $query->where('id', 'LIKE', $searchString)
                    ->orWhere('full_name', 'LIKE', $searchString)
                    ->orWhere('email', 'LIKE', $searchString);
            });
    }

    /**
     * Retrieve all records that match the specified user role and search term.
     *
     * @param string $role The user role to filter by
     * @param string $search The search term to search with
     * @return mixed
     */
    public function getByRoleAndSearch($role, $search)
    {
        return $this->queryByRoleAndSearch($role, $search)->all();
    }

    /**
     * Retrieve a paginated list of all the user records that match the specified
     * user role and search term.
     *
     * @param string $role The user role to filter by
     * @param string $search The search term to search with
     * @param int $count The number of records per page.
     * @return \Illuminate\Pagination\Paginator
     */
    public function getPaginatedByRoleAndSearch($role, $search, $count = 15)
    {
        return $this->queryByRoleAndSearch($role, $search)->paginate($count);
    }

    public function getValidRoles()
    {
        return $this->model->validRoles();
    }

}