<?php

namespace App\Http\Controllers;

use App\Http\Requests\Course\CreateCourse;
use App\Http\Requests\Course\AddProfessorInCourse;
use App\Http\Requests\Course\AddStudentInCourse;
use App\Http\Requests\Course\AttachCourseToDepartments;
use App\Http\Requests\Course\UpdateCourse;
use App\Http\Resources\CourseResource;
use App\Http\Resources\ProfessorResource;
use App\Http\Resources\StudentResource;
use App\Models\Course;
use App\Models\Professor;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CourseController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Course::class);
        return CourseResource::collection(Course::paginate());
    }


    public function getStudents(Course $course)
    {
        $this->authorize('view', $course);
        $studentsInCourse = $course->students()->paginate();
        return StudentResource::collection($studentsInCourse);
    }

    public function addStudent(AddStudentInCourse $request, Course $course)
    {
        $this->authorize('toggleCourseMembers', Course::class);
        $validated = $request->validated();
        $course->students()->syncWithoutDetaching($validated['id']);
        return ['message' => "successfully added student in course."];
    }

    public function removeStudent(Course $course, Student $student)
    {
        $this->authorize('toggleCourseMembers', Course::class);
        $course->students()->detach($student->id);
        return ['message' => "successfully removed student in course."];
    }

    public function getProfessors(Course $course)
    {
        $this->authorize('view', $course);
        $professorsInCourse = $course->professors()->paginate();
        return ProfessorResource::collection($professorsInCourse);
    }

    public function addProfessor(AddProfessorInCourse $request, Course $course)
    {
        $this->authorize('toggleCourseMembers', Course::class);
        $validated = $request->validated();
        $course->professors()->syncWithoutDetaching($validated['id']);
        return ['message' => "successfully added professor in course."];
    }

    public function removeProfessor(Course $course, Professor $professor)
    {
        $this->authorize('toggleCourseMembers', Course::class);
        $course->professors()->detach($professor->id);
        return ['message' => "successfully removed professor in course."];
    }

    public function store(CreateCourse $request)
    {
        $this->authorize('create', Course::class);
        $validated = $request->validated();
        $newCourse = Course::create(['name' => $validated['name'], 'code' => $validated['code']]);
        $newCourse->departments()->attach($validated['department_ids']);
        return new CourseResource($newCourse->load(['departments' => function ($q) {
            $q->select('departments.id', 'departments.name', 'departments.code');
        }]));
    }

    public function show(Course $course)
    {
        $this->authorize('view', $course);
        return new CourseResource($course->load(['departments' => function ($q) {
            $q->select('departments.id', 'departments.name', 'departments.code');
        }]));
    }

    public function update(UpdateCourse $request, Course $course)
    {
        $this->authorize('update', Course::class);
        $validated = $request->validated();

        if (isset($validated['department_ids'])) {
            $oldDepartmentIds = $course->departments()->pluck('departments.id')->toArray();
            $newDepartmentIds = $validated['department_ids'];
            //update the course's departmentIds with the new ones
            $course->departments()->sync($newDepartmentIds);

            //get the department_ids that were removed (present in oldDepartmentIds, but not in newDepartmentIds)
            $removedDepartmentIds = array_diff($oldDepartmentIds, $newDepartmentIds);

            if (!empty($removedDepartmentIds)) {
                //find students that has department_id in $removedDepartmentIds (belongs to the removed department ids)
                $studentsToRemove = $course->students()
                    ->whereIn('department_id', $removedDepartmentIds)
                    ->pluck('students.id')
                    ->toArray();

                //if has studentsToRemove, remove/detach those students from the course.
                if (!empty($studentsToRemove)) {
                    $course->students()->detach($studentsToRemove);
                }
            }
        }

        $course->update(['name' => $validated['name'], 'code' => $validated['code']]);
        return new CourseResource($course->load(['departments' => function ($q) {
            $q->select('departments.id', 'departments.name', 'departments.code');
        }]));
    }

    public function destroy(Course $course)
    {
        $this->authorize('delete', Course::class);
        $this->authorize('forceDelete', Course::class);

        Course::destroy($course->id);

        return ['message' => 'course deleted.'];
    }
}
