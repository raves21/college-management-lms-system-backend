<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\ProfessorController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::get('/admins', [AdminController::class, 'index']);
    Route::apiResources([
        'students' => StudentController::class,
        'professors' => ProfessorController::class,
        'departments' => DepartmentController::class,
        'courses' => CourseController::class,
    ]);

    Route::controller(DepartmentController::class)->prefix('departments/{department}')->group(function () {
        Route::prefix('courses')->group(function () {
            Route::get('/', 'getCourses');
            Route::post('/{course}', 'addCourseToDepartment');
            Route::delete('/{course}', 'removeCourseFromDepartment');
        });
        Route::get('/students', 'getStudents');
        Route::get('/professors', 'getProfessors');
    });

    Route::controller(CourseController::class)->prefix('courses/{course}')->group(function () {
        Route::prefix('students')->group(function () {
            Route::get('/', 'getStudents');
            Route::post('/', 'addStudent');
            Route::delete('/{student}', 'removeStudent');
        });
        Route::get('/studentsThatCanBeAdded', 'getStudentsThatCanBeAdded');

        Route::prefix('professors')->group(function () {
            Route::get('/', 'getProfessors');
            Route::post('/', 'addProfessor');
            Route::delete('/{professor}', 'removeProfessor');
        });
        Route::get('/professorsThatCanBeAdded', 'getProfessorsThatCanBeAdded');
    });
});
