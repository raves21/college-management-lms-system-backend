<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\Login;
use App\Http\Resources\AdminResource;
use App\Http\Resources\ProfessorResource;
use App\Http\Resources\StudentResource;
use App\Http\Resources\UserResource;
use App\Models\Admin;
use App\Models\Professor;
use App\Models\Student;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function login(Login $request)
    {
        $validated = $request->validated();
        $foundUser = User::where('email', $validated['email'])->first();

        if (!$foundUser || !Hash::check($validated['password'], $foundUser->password)) {
            return response()->json(['error' => 'invalid credentials.']);
        }

        $userType = $foundUser->user_type_id;
        $apiToken = $foundUser->createToken($foundUser->email)->plainTextToken;

        if ($userType === UserType::ADMIN) {
            $admin = Admin::where('user_id', $foundUser->id)->first();
            return ['user' => new AdminResource($admin), 'token' => $apiToken,];
        } else if ($userType === UserType::PROFESSOR) {
            $professor = Professor::where('user_id', $foundUser->id)->first();
            return ['user' => new ProfessorResource($professor), 'token' => $apiToken,];
        } else {
            $student = Student::where('user_id', $foundUser->id)->first();
            return ['user' => new StudentResource($student), 'token' => $apiToken];
        }
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return ['message' => 'logged out successfully.'];
    }

    public function me()
    {
        $currentUser = User::find(auth()->user()->id);
        switch (auth()->user()->user_type_id) {
            case UserType::ADMIN:
                $currentUserAdmin = Admin::where('user_id', $currentUser->id)->first();
                return new AdminResource($currentUserAdmin);
            case UserType::STUDENT:
                $currentUserStudent = Student::where('user_id', $currentUser->id)->first();
                Log::info($currentUserStudent);
                return new StudentResource($currentUserStudent);
            case UserType::PROFESSOR:
                $currentUserProfessor = Professor::where('user_id', $currentUser->id)->first();
                return new StudentResource($currentUserProfessor);
        }
    }
}
