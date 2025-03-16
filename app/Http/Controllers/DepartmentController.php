<?php

namespace App\Http\Controllers;

use App\Http\Requests\Department\CreateDepartment;
use App\Http\Requests\Department\UpdateDepartment;
use App\Http\Resources\CourseResource;
use App\Http\Resources\DepartmentResource;
use App\Http\Resources\ProfessorResource;
use App\Http\Resources\StudentResource;
use App\Models\Course;
use App\Models\Department;
use Illuminate\Support\Facades\Log;

class DepartmentController extends Controller
{

    public function index()
    {
        $this->authorize('viewAny', Department::class);
        return DepartmentResource::collection(Department::paginate());
    }

    public function getCourses(Department $department)
    {
        $this->authorize('getDepartmentMembers', $department);
        $departmentCourses = $department->courses()->with('departments:id,name,code')->paginate();
        return CourseResource::collection($departmentCourses);
    }

    public function addCourseToDepartment(Course $course, Department $department)
    {
        $department->courses()->syncWithoutDetaching($course);
        return ['message' => "course {$course->id} successfully added to department {$department->id}"];
    }

    public function removeCourseFromDepartment(Course $course, Department $department)
    {
        $this->authorize('removeCourseFromDepartment', $department);
        //get all students' ids from this course that belong to the department
        $studentsFromDepartment = $course->students()->where('department_id', $department->id)->pluck('id');

        //get all professors' ids from this course that belong to the department
        $profsFromDepartment = $course->professors()->where('department_id', $department->id)->pluck('id');

        //detach the retrieved students from the course
        $course->students()->detach($studentsFromDepartment);

        //detach the retrieved professors from the course
        $course->professors()->detach($profsFromDepartment);

        //detach the course from the department
        $course->departments()->detach($department->id);

        return ['message' => "course {$course->id} successfully removed from department {$department->id}."];
    }

    public function getProfessors(Department $department)
    {
        $this->authorize('getDepartmentMembers', $department);
        $departmentProfessors = $department->professors()->paginate();
        return ProfessorResource::collection($departmentProfessors);
    }

    public function getStudents(Department $department)
    {
        $this->authorize('getDepartmentMembers', $department);
        $departmentStudents = $department->students()->paginate();
        return StudentResource::collection($departmentStudents);
    }

    public function store(CreateDepartment $request)
    {
        $this->authorize('create', Department::class);
        $validated = $request->validated();
        $newDepartment = Department::create($validated);
        return new DepartmentResource($newDepartment);
    }

    public function show(Department $department)
    {
        $this->authorize('view', $department);
        return new DepartmentResource($department);
    }

    public function update(UpdateDepartment $request, Department $department)
    {
        $this->authorize('update', Department::class);
        $validated = $request->validated();
        $department->update($validated);
        return new DepartmentResource($department);
    }

    public function destroy(Department $department)
    {
        $this->authorize('delete', Department::class);
        $this->authorize('forceDelete', Department::class);
        Department::destroy($department->id);
        return ['message' => 'deleted successfully.'];
    }
}
