<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\School\Http\Controllers\Api\Academic\AcademicController;
use Modules\School\Http\Controllers\Api\Academic\AssessmentController;
use Modules\School\Http\Controllers\Api\Academic\AttendanceController;
use Modules\School\Http\Controllers\Api\Academic\P5ProjectController;
use Modules\School\Http\Controllers\Api\Academic\ReportCardController;
use Modules\School\Http\Controllers\Api\Academic\ScheduleController;
use Modules\School\Http\Controllers\Api\Academic\TeachingJournalController;
use Modules\School\Http\Controllers\Api\Counseling\CounselingController;
use Modules\School\Http\Controllers\Api\HR\StaffController;
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

    // --- Pendidik & Tenaga Kependidikan (HR / Staff) ---
    Route::prefix('staff')->group(function (): void {
        Route::get('/shifts/list', [StaffController::class, 'getShifts']);
        Route::post('/shifts', [StaffController::class, 'storeShift']);
        Route::get('/', [StaffController::class, 'index']);
        Route::post('/', [StaffController::class, 'store']);
        Route::get('/{id}', [StaffController::class, 'show']);
        Route::put('/{id}', [StaffController::class, 'update']);
        Route::delete('/{id}', [StaffController::class, 'destroy']);
    });

    // --- Presensi Harian Siswa & Staff ---
    Route::prefix('attendance')->group(function (): void {
        Route::get('/students', [AttendanceController::class, 'getStudentAttendance']);
        Route::post('/students/bulk', [AttendanceController::class, 'saveBulkStudentAttendance']);
        Route::get('/staff', [AttendanceController::class, 'getStaffAttendance']);
        Route::post('/staff/check-in', [AttendanceController::class, 'recordStaffCheckIn']);
        Route::post('/staff/check-out', [AttendanceController::class, 'recordStaffCheckOut']);
    });

    // --- Jadwal Pelajaran (Schedules) ---
    Route::prefix('schedules')->group(function (): void {
        Route::get('/', [ScheduleController::class, 'index']);
        Route::post('/', [ScheduleController::class, 'store']);
        Route::put('/{id}', [ScheduleController::class, 'update']);
        Route::delete('/{id}', [ScheduleController::class, 'destroy']);
    });

    // --- Jurnal Harian Mengajar Guru (KBM) ---
    Route::prefix('journals')->group(function (): void {
        Route::get('/', [TeachingJournalController::class, 'index']);
        Route::post('/', [TeachingJournalController::class, 'store']);
        Route::get('/{id}', [TeachingJournalController::class, 'show']);
        Route::put('/{id}', [TeachingJournalController::class, 'update']);
        Route::delete('/{id}', [TeachingJournalController::class, 'destroy']);
    });

    // --- Penilaian & Asesmen Kurikulum Merdeka ---
    Route::prefix('assessments')->group(function (): void {
        Route::get('/', [AssessmentController::class, 'index']);
        Route::post('/', [AssessmentController::class, 'store']);
        Route::get('/{id}', [AssessmentController::class, 'show']);
        Route::delete('/{id}', [AssessmentController::class, 'destroy']);
        Route::get('/{id}/grades', [AssessmentController::class, 'grades']);
        Route::post('/{id}/grades/bulk', [AssessmentController::class, 'storeBulkGrades']);
    });

    // --- Leger Nilai & Raport Siswa ---
    Route::prefix('reports')->group(function (): void {
        Route::get('/ledger', [ReportCardController::class, 'ledger']);
        Route::post('/calculate', [ReportCardController::class, 'calculate']);
        Route::get('/student/{studentId}', [ReportCardController::class, 'studentReport']);
    });

    // --- Proyek Penguatan Profil Pelajar Pancasila (P5) ---
    Route::prefix('p5')->group(function (): void {
        Route::get('/projects', [P5ProjectController::class, 'index']);
        Route::post('/projects', [P5ProjectController::class, 'store']);
        Route::get('/projects/{id}', [P5ProjectController::class, 'show']);
        Route::delete('/projects/{id}', [P5ProjectController::class, 'destroy']);
        Route::post('/projects/{id}/assessments/bulk', [P5ProjectController::class, 'storeBulkAssessments']);
    });

    // --- Layanan Kasus Bimbingan Konseling (BK) ---
    Route::prefix('counseling')->group(function (): void {
        Route::get('/cases', [CounselingController::class, 'index']);
        Route::post('/cases', [CounselingController::class, 'store']);
        Route::get('/cases/{id}', [CounselingController::class, 'show']);
        Route::put('/cases/{id}', [CounselingController::class, 'update']);
        Route::post('/cases/{id}/resolve', [CounselingController::class, 'resolve']);
        Route::delete('/cases/{id}', [CounselingController::class, 'destroy']);
    });
});
