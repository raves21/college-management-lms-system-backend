<?php

namespace App\Http\Controllers;

use App\Http\Requests\Professor\CreateProfessor;
use App\Http\Requests\Professor\UpdateProfessor;
use App\Http\Resources\ProfessorResource;
use App\Models\Professor;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;

class ProfessorController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Professor::class);
        return ProfessorResource::collection(Professor::paginate());
    }

    public function store(CreateProfessor $request)
    {
        $this->authorize('create', Professor::class);
        $validated = $request->validated();
        $newProfessor = User::create([...$validated, 'user_type_id' => UserType::PROFESSOR])
            ->professor()
            ->create(['department_id' => $validated['department_id']]);
        return new ProfessorResource($newProfessor->load(['user', 'department']));
    }

    public function show(Professor $professor)
    {
        $this->authorize('viewAny', Professor::class);
        return new ProfessorResource($professor);
    }

    public function update(UpdateProfessor $request, Professor $professor)
    {
        $this->authorize('update', Professor::class);

        $validated = $request->validated();

        //if department_id is set, update the professor's department_id
        if (isset($validated['department_id'])) {
            $professor->update(['department_id' => $validated['department_id']]);
        }

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        //update the professor's user related model
        $professor->user->update(Arr::only($validated, ['email', 'first_name', 'last_name', 'password']));
        return new ProfessorResource($professor->load(['user', 'department' => function ($q) {
            $q->select('id', 'name', 'code');
        }]));
    }

    public function destroy(Professor $professor)
    {
        $this->authorize('delete', Professor::class);
        $this->authorize('forceDelete', Professor::class);

        Professor::destroy($professor->id);
        return ['message' => 'deleted successfully.'];
    }
}
