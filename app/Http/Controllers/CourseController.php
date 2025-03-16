<?php

namespace App\Http\Controllers;

use App\Http\Requests\Course\CreateCourse;
use App\Http\Requests\Course\AddProfessorInCourse;
use App\Http\Requests\Course\AddStudentInCourse;
use App\Http\Requests\Course\UpdateCourse;
use App\Http\Resources\CourseResource;
use App\Http\Resources\ProfessorResource;
use App\Http\Resources\StudentResource;
use App\Models\Course;
use App\Models\Professor;
use App\Models\Student;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class CourseController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Course::class);
        return CourseResource::collection(Course::with('departments')->paginate());
    }


    public function getStudents(Course $course)
    {
        $this->authorize('view', $course);
        $studentsInCourse = $course->students()->with('department:id,name,code')->paginate();
        return StudentResource::collection($studentsInCourse);
    }

    public function addStudent(AddStudentInCourse $request, Course $course)
    {
        $this->authorize('toggleCourseMembers', Course::class);
        $validated = $request->validated();
        $student = Student::find($validated['id']);
        //check if student's department is in the course's departments
        $studentDeptInCourseDepts = $course->departments()->where('departments.id', $student->department_id)->exists();
        if (!$studentDeptInCourseDepts) {
            return response()->json(['error' => 'Invalid. Student does not belong in the course\'s departments.'], 400);
        }
        $course->students()->syncWithoutDetaching($student->id);
        return ['message' => "successfully added student {$student->id} to course {$course->id}."];
    }

    public function removeStudent(Course $course, Student $student)
    {
        $this->authorize('toggleCourseMembers', Course::class);
        $course->students()->detach($student->id);
        return ['message' => "successfully removed student {$student->id} from course {$course->id}."];
    }

    public function getProfessors(Course $course)
    {
        $this->authorize('view', $course);
        $professorsInCourse = $course->professors()->with(['department' => fn($q) => $q->select('name', 'code')])->paginate();
        return ProfessorResource::collection($professorsInCourse);
    }

    public function addProfessor(AddProfessorInCourse $request, Course $course)
    {
        $this->authorize('toggleCourseMembers', Course::class);
        $validated = $request->validated();
        $course->professors()->syncWithoutDetaching($validated['id']);
        return ['message' => "successfully added professor " . $validated['id'] . "to course {$course->id}."];
    }

    public function removeProfessor(Course $course, Professor $professor)
    {
        $this->authorize('toggleCourseMembers', Course::class);
        $course->professors()->detach($professor->id);
        return ['message' => "successfully removed professor {$professor->id} from course {$course->id}."];
    }

    public function getStudentsThatCanBeAdded(Course $course)
    {
        $this->authorize('getMembersThatCanBeAdded', Course::class);
        $studentsThatCanBeAdded = Student::whereDoesntHave('courses', function ($query) use ($course) {
            $query->where('course_id', $course->id);
        })
            ->whereIn('department_id', $course->departments->pluck('id'))
            ->with('department:id,name,code')
            ->get();
        return StudentResource::collection($studentsThatCanBeAdded);
    }

    public function getProfessorsThatCanBeAdded(Course $course)
    {
        $this->authorize('getMembersThatCanBeAdded', Course::class);
        $professorsThatCanBeAdded = Professor::whereDoesntHave('courses', function ($query) use ($course) {
            $query->where('course_id', $course->id);
        })
            ->with('department:id,name,code')
            ->get();
        return ProfessorResource::collection($professorsThatCanBeAdded);
    }

    public function store(CreateCourse $request)
    {
        $this->authorize('create', Course::class);
        $validated = $request->validated();
        $newCourse = Course::create(['name' => $validated['name'], 'code' => $validated['code']]);
        $newCourse->departments()->attach($validated['department_ids']);
        return new CourseResource($newCourse->load('departments:id,name,code'));
    }

    public function show(Course $course)
    {
        $this->authorize('view', $course);
        return new CourseResource($course->load('departments:id,name,code'));
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

        $course->update(Arr::only($validated, ['name', 'code']));
        return new CourseResource($course->load('departments:id,name,code'));
    }

    public function destroy(Course $course)
    {
        $this->authorize('delete', Course::class);
        $this->authorize('forceDelete', Course::class);

        Course::destroy($course->id);

        return ['message' => 'course deleted.'];
    }
}
