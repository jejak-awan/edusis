<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\School\Http\Controllers\Api\Academic\AcademicController;
use Modules\School\Http\Controllers\Api\SchoolDashboardController;
use Modules\School\Http\Controllers\Api\Student\DisciplineController;
use Modules\School\Http\Controllers\Api\Student\StudentController;

Route::prefix('v1/school')->middleware(['auth:sanctum'])->group(function (): void {
    // Health / Ping
    Route::get('/ping', function () {
        return response()->json([
            'status' => 'success',
            'message' => 'JA-EduSIS School Module operational',
            'timestamp' => now()->toIso8601String(),
        ]);
    });

    // Dashboard Overview
    Route::get('/dashboard', [SchoolDashboardController::class, 'getOverview']);

    // --- Academic Master Data ---
    Route::prefix('academic')->group(function (): void {
        // Years & Semesters
        Route::get('/years', [AcademicController::class, 'getYears']);
        Route::post('/years', [AcademicController::class, 'storeYear']);
        Route::post('/years/{id}/activate', [AcademicController::class, 'activateYear']);
        Route::delete('/years/{id}', [AcademicController::class, 'deleteYear']);
        Route::get('/years/{yearId}/semesters', [AcademicController::class, 'getSemesters']);
        Route::post('/semesters/{id}/activate', [AcademicController::class, 'activateSemester']);

        // Levels
        Route::get('/levels', [AcademicController::class, 'getLevels']);

        // Departments (Jurusan)
        Route::get('/departments', [AcademicController::class, 'getDepartments']);
        Route::post('/departments', [AcademicController::class, 'storeDepartment']);
        Route::put('/departments/{id}', [AcademicController::class, 'updateDepartment']);
        Route::delete('/departments/{id}', [AcademicController::class, 'deleteDepartment']);

        // Subjects (Mapel)
        Route::get('/subjects', [AcademicController::class, 'getSubjects']);
        Route::post('/subjects', [AcademicController::class, 'storeSubject']);
        Route::put('/subjects/{id}', [AcademicController::class, 'updateSubject']);
        Route::delete('/subjects/{id}', [AcademicController::class, 'deleteSubject']);

        // Study Groups (Rombel)
        Route::get('/study-groups', [AcademicController::class, 'getStudyGroups']);
        Route::post('/study-groups', [AcademicController::class, 'storeStudyGroup']);
        Route::get('/study-groups/{id}', [AcademicController::class, 'showStudyGroup']);
        Route::put('/study-groups/{id}', [AcademicController::class, 'updateStudyGroup']);
        Route::delete('/study-groups/{id}', [AcademicController::class, 'deleteStudyGroup']);
        Route::post('/study-groups/{id}/members', [AcademicController::class, 'assignStudentToGroup']);
        Route::delete('/study-groups/{id}/members/{studentId}', [AcademicController::class, 'removeStudentFromGroup']);
    });

    // --- Student Affairs (Kesiswaan Dapodik) ---
    Route::prefix('students')->group(function (): void {
        Route::get('/', [StudentController::class, 'index']);
        Route::post('/', [StudentController::class, 'store']);
        Route::get('/{id}', [StudentController::class, 'show']);
        Route::put('/{id}', [StudentController::class, 'update']);
        Route::delete('/{id}', [StudentController::class, 'destroy']);
    });

    // --- Discipline & SISKA Points ---
    Route::prefix('discipline')->group(function (): void {
        Route::get('/categories', [DisciplineController::class, 'getCategories']);
        Route::post('/categories', [DisciplineController::class, 'storeCategory']);
        Route::post('/record', [DisciplineController::class, 'recordPoint']);
        Route::get('/violations', [DisciplineController::class, 'getViolations']);
        Route::post('/violations/{id}/resolve', [DisciplineController::class, 'resolveViolation']);
        Route::get('/achievements', [DisciplineController::class, 'getAchievements']);
    });
});
