<?php

namespace App\Http\Controllers;

use App\Http\Requests\Student\CreateStudent;
use App\Http\Requests\Student\UpdateStudent;
use App\Http\Resources\StudentResource;
use App\Models\Student;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{

    public function index()
    {
        $this->authorize('viewAny', Student::class);
        return StudentResource::collection(Student::with('department')->paginate());
    }

    public function store(CreateStudent $request)
    {
        $this->authorize('create', Student::class);
        $validated = $request->validated();
        $validated['password'] = Hash::make($validated['password']);
        $newStudent = User::create([...$validated, 'user_type_id' => UserType::STUDENT])
            ->student()
            ->create(['department_id' => $validated['department_id']]);
        return new StudentResource($newStudent->load([
            'user',
            'department:id,name,code'
        ]));
    }

    public function show(Student $student)
    {
        $this->authorize('view', Student::class);
        return new StudentResource($student);
    }

    public function update(UpdateStudent $request, Student $student)
    {
        $this->authorize('update', Student::class);
        $validated = $request->validated();

        //if department_id is set, update the student's department_id
        if (isset($validated['department_id'])) {
            $student->update(Arr::only($validated, ['department_id']));
        }

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        //update the student's user related model
        $student->user->update(Arr::only($validated, ['email', 'first_name', 'last_name', 'password']));
        return new StudentResource($student->load([
            'user',
            'department:id,name,code'
        ]));
    }

    public function destroy(Student $student)
    {
        $this->authorize('delete', Student::class);
        $this->authorize('forceDelete', Student::class);
        Student::destroy($student->id);
        return ['message' => 'deleted successfully.'];
    }
}
