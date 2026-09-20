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
use Modules\School\Http\Controllers\Api\Administration\GuestBookController;
use Modules\School\Http\Controllers\Api\Administration\LetterController;
use Modules\School\Http\Controllers\Api\Administration\PpdbController;
use Modules\School\Http\Controllers\Api\Communication\WhatsAppController;
use Modules\School\Http\Controllers\Api\Counseling\CounselingController;
use Modules\School\Http\Controllers\Api\Facilities\FacilityController;
use Modules\School\Http\Controllers\Api\HR\StaffController;
use Modules\School\Http\Controllers\Api\Integration\KatampiIntegrationController;
use Modules\School\Http\Controllers\Api\SchoolDashboardController;
use Modules\School\Http\Controllers\Api\Student\DisciplineController;
use Modules\School\Http\Controllers\Api\Student\StudentController;
use Modules\School\Http\Controllers\Api\StudentAffairs\ExtracurricularController;
use Modules\School\Http\Controllers\Api\StudentAffairs\OsisElectionController;
use Modules\School\Http\Controllers\Api\StudentAffairs\VotingEngineController;

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

    // --- Administrasi Persuratan & Disposisi TU ---
    Route::prefix('letters')->group(function (): void {
        Route::get('/generate-number', [LetterController::class, 'generateNumber']);
        Route::get('/', [LetterController::class, 'index']);
        Route::post('/', [LetterController::class, 'store']);
        Route::get('/{id}', [LetterController::class, 'show']);
        Route::put('/{id}', [LetterController::class, 'update']);
        Route::post('/{id}/disposition', [LetterController::class, 'updateDisposition']);
        Route::delete('/{id}', [LetterController::class, 'destroy']);
    });

    // --- PPDB Online ---
    Route::prefix('ppdb')->group(function (): void {
        Route::get('/', [PpdbController::class, 'index']);
        Route::post('/', [PpdbController::class, 'store']);
        Route::get('/{id}', [PpdbController::class, 'show']);
        Route::put('/{id}', [PpdbController::class, 'update']);
        Route::post('/{id}/verify', [PpdbController::class, 'verify']);
        Route::post('/{id}/admit', [PpdbController::class, 'admit']);
        Route::delete('/{id}', [PpdbController::class, 'destroy']);
    });

    // --- Buku Tamu Resepsionis / Keamanan ---
    Route::prefix('guest-book')->group(function (): void {
        Route::get('/', [GuestBookController::class, 'index']);
        Route::post('/', [GuestBookController::class, 'store']);
        Route::get('/{id}', [GuestBookController::class, 'show']);
        Route::put('/{id}', [GuestBookController::class, 'update']);
        Route::post('/{id}/checkout', [GuestBookController::class, 'checkout']);
        Route::delete('/{id}', [GuestBookController::class, 'destroy']);
    });

    // --- OSIS & E-Voting (Legacy Specific Endpoints) ---
    Route::prefix('osis')->group(function (): void {
        Route::get('/elections', [OsisElectionController::class, 'index']);
        Route::post('/elections', [OsisElectionController::class, 'store']);
        Route::get('/elections/{id}', [OsisElectionController::class, 'show']);
        Route::put('/elections/{id}', [OsisElectionController::class, 'update']);
        Route::delete('/elections/{id}', [OsisElectionController::class, 'destroy']);
        Route::post('/elections/{id}/candidates', [OsisElectionController::class, 'addCandidate']);
        Route::post('/elections/{id}/vote', [OsisElectionController::class, 'vote']);
        Route::get('/elections/{id}/quick-count', [OsisElectionController::class, 'quickCount']);
    });

    // --- Generic Reusable E-Voting & Polling Engine ---
    Route::prefix('voting')->group(function (): void {
        Route::get('/events', [VotingEngineController::class, 'getEvents']);
        Route::post('/events', [VotingEngineController::class, 'storeEvent']);
        Route::get('/events/{id}', [VotingEngineController::class, 'showEvent']);
        Route::put('/events/{id}', [VotingEngineController::class, 'updateEvent']);
        Route::delete('/events/{id}', [VotingEngineController::class, 'destroyEvent']);
        Route::post('/events/{id}/candidates', [VotingEngineController::class, 'addCandidate']);
        Route::post('/events/{id}/generate-voters', [VotingEngineController::class, 'generateVoters']);
        Route::get('/events/{id}/voters', [VotingEngineController::class, 'getVoters']);
        Route::post('/events/{id}/vote', [VotingEngineController::class, 'castVote']);
        Route::get('/events/{id}/quick-count', [VotingEngineController::class, 'quickCount']);
    });

    // --- Ekstrakurikuler ---
    Route::prefix('extracurriculars')->group(function (): void {
        Route::get('/', [ExtracurricularController::class, 'index']);
        Route::post('/', [ExtracurricularController::class, 'store']);
        Route::get('/{id}', [ExtracurricularController::class, 'show']);
        Route::put('/{id}', [ExtracurricularController::class, 'update']);
        Route::delete('/{id}', [ExtracurricularController::class, 'destroy']);
    });

    // --- Sarana & Prasarana (Facilities) ---
    Route::prefix('facilities')->group(function (): void {
        // Rooms
        Route::get('/rooms', [FacilityController::class, 'getRooms']);
        Route::post('/rooms', [FacilityController::class, 'storeRoom']);
        Route::put('/rooms/{id}', [FacilityController::class, 'updateRoom']);
        Route::delete('/rooms/{id}', [FacilityController::class, 'deleteRoom']);

        // Assets
        Route::get('/assets', [FacilityController::class, 'getAssets']);
        Route::post('/assets', [FacilityController::class, 'storeAsset']);
        Route::put('/assets/{id}', [FacilityController::class, 'updateAsset']);
        Route::delete('/assets/{id}', [FacilityController::class, 'deleteAsset']);

        // Maintenance Tickets
        Route::get('/tickets', [FacilityController::class, 'getTickets']);
        Route::post('/tickets', [FacilityController::class, 'storeTicket']);
        Route::put('/tickets/{id}', [FacilityController::class, 'updateTicket']);
        Route::post('/tickets/{id}/resolve', [FacilityController::class, 'resolveTicket']);
    });

    // --- Integrasi KataMPI CBT ---
    Route::prefix('integrations/katampi')->group(function (): void {
        Route::get('/config', [KatampiIntegrationController::class, 'getConfig']);
        Route::put('/config', [KatampiIntegrationController::class, 'updateConfig']);
        Route::post('/sso-token', [KatampiIntegrationController::class, 'generateSsoToken']);
        Route::get('/roster', [KatampiIntegrationController::class, 'getRoster']);
        Route::post('/sync-grades', [KatampiIntegrationController::class, 'syncGrades']);
    });

    // --- WhatsApp Gateway & Komunikasi Orang Tua ---
    Route::prefix('communications/whatsapp')->group(function (): void {
        Route::get('/logs', [WhatsAppController::class, 'getLogs']);
        Route::get('/stats', [WhatsAppController::class, 'getStats']);
        Route::post('/send', [WhatsAppController::class, 'sendMessage']);
        Route::post('/alerts/attendance', [WhatsAppController::class, 'sendAttendanceAlerts']);
        Route::post('/logs/{id}/resend', [WhatsAppController::class, 'resendLog']);
    });
});
