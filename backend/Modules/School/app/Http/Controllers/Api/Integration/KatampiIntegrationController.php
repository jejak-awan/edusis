<?php

declare(strict_types=1);

namespace Modules\School\Http\Controllers\Api\Integration;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\School\Http\Controllers\Api\Common\BaseController;
use Modules\School\Models\Academic\Assessment;
use Modules\School\Models\Academic\Grade;
use Modules\School\Models\Academic\StudyGroup;
use Modules\School\Models\Facilities\Room;
use Modules\School\Models\HR\Staff;
use Modules\School\Models\Institution\School;
use Modules\School\Models\Integration\KatampiConfig;
use Modules\School\Models\Student\Student;

class KatampiIntegrationController extends BaseController
{
    private function resolveSchoolId(Request $request): int
    {
        $schoolId = $request->header('X-School-Id');
        if ($schoolId && is_numeric($schoolId)) {
            return (int) $schoolId;
        }

        $school = School::first();
        return $school ? $school->id : 1;
    }

    public function getConfig(Request $request): JsonResponse
    {
        $schoolId = $this->resolveSchoolId($request);
        $config = KatampiConfig::firstOrCreate(
            ['school_id' => $schoolId],
            [
                'cbt_url' => 'http://ja-katampi:8787',
                'api_key' => bin2hex(random_bytes(16)),
                'is_active' => true,
                'sync_stats' => ['total_exams' => 0, 'total_grades_synced' => 0],
            ]
        );

        return $this->sendResponse($config, 'KataMPI configuration retrieved');
    }

    public function updateConfig(Request $request): JsonResponse
    {
        $schoolId = $this->resolveSchoolId($request);

        $validated = $request->validate([
            'cbt_url' => 'required|url|max:255',
            'api_key' => 'nullable|string|max:255',
            'is_active' => 'required|boolean',
        ]);

        $config = KatampiConfig::updateOrCreate(
            ['school_id' => $schoolId],
            [
                'cbt_url' => $validated['cbt_url'],
                'api_key' => $validated['api_key'] ?? bin2hex(random_bytes(16)),
                'is_active' => $validated['is_active'],
            ]
        );

        return $this->sendResponse($config, 'KataMPI configuration updated');
    }

    public function generateSsoToken(Request $request): JsonResponse
    {
        $schoolId = $this->resolveSchoolId($request);
        $config = KatampiConfig::where('school_id', $schoolId)->first();
        $school = School::find($schoolId);

        $apiKey = $config?->api_key ?? config('app.key');
        $user = $request->user();

        $studentId = $request->input('student_id');
        $student = null;
        if ($studentId) {
            $student = Student::where('school_id', $schoolId)->with('studyGroups')->find($studentId);
        }

        $studentGroup = $student?->studyGroups?->first();

        $payload = [
            'sub' => $user ? $user->id : ($student ? $student->id : 1),
            'name' => $student ? $student->full_name : ($user ? $user->name : 'EduSIS User'),
            'username' => $student ? $student->nisn : ($user ? $user->username : 'edusis_user'),
            'role' => $student ? 'student' : ($user && isset($user->role) ? $user->role : 'teacher'),
            'student_id' => $student?->id,
            'nisn' => $student?->nisn,
            'study_group' => $studentGroup?->name,
            'school_id' => $schoolId,
            'school_name' => $school?->name ?? 'EduSIS School',
            'iat' => time(),
            'exp' => time() + 3600, // 1 hour token
        ];

        $jsonPayload = json_encode($payload, JSON_UNESCAPED_SLASHES);
        $signature = hash_hmac('sha256', $jsonPayload, $apiKey);
        $token = base64_encode($jsonPayload) . '.' . $signature;

        $cbtBaseUrl = rtrim($config?->cbt_url ?? 'http://ja-katampi:8787', '/');
        $redirectUrl = "{$cbtBaseUrl}/sso?token={$token}";

        return $this->sendResponse([
            'token' => $token,
            'redirect_url' => $redirectUrl,
            'expires_in' => 3600,
            'user' => $payload,
        ], 'KataMPI SSO token generated successfully');
    }

    public function getRoster(Request $request): JsonResponse
    {
        $schoolId = $this->resolveSchoolId($request);

        $query = Student::where('school_id', $schoolId)
            ->where('status', 'aktif')
            ->with(['studyGroups:id,name,level,major']);

        if ($request->filled('study_group_id')) {
            $groupId = $request->input('study_group_id');
            $query->whereHas('studyGroups', function ($q) use ($groupId) {
                $q->where('study_group_id', $groupId);
            });
        }

        $students = $query->orderBy('full_name')->get();
        $school = School::find($schoolId);

        $roster = $students->map(function ($student) use ($school) {
            $group = $student->studyGroups->first();
            return [
                'id' => $student->id,
                'nisn' => $student->nisn,
                'nis' => $student->nis,
                'name' => $student->full_name,
                'gender' => $student->gender,
                'study_group_id' => $group?->id,
                'study_group_name' => $group?->name ?? 'Unassigned',
                'level' => $group?->level,
                'school_id' => $school?->id,
                'school_name' => $school?->name ?? 'EduSIS School',
            ];
        });

        return $this->sendResponse([
            'total_students' => $roster->count(),
            'school' => $school?->name,
            'students' => $roster,
        ], 'KataMPI Roster export generated successfully');
    }

    public function syncGrades(Request $request): JsonResponse
    {
        $schoolId = $this->resolveSchoolId($request);

        $validated = $request->validate([
            'assessment_id' => 'nullable|exists:sch_acad_assessments,id',
            'assessment_title' => 'required_without:assessment_id|nullable|string|max:150',
            'subject_id' => 'required_without:assessment_id|nullable|exists:sch_acad_subjects,id',
            'study_group_id' => 'required_without:assessment_id|nullable|exists:sch_acad_study_groups,id',
            'teacher_id' => 'nullable|exists:sch_hr_staff,id',
            'academic_year_id' => 'nullable|exists:sch_acad_academic_years,id',
            'semester_id' => 'nullable|exists:sch_acad_semesters,id',
            'type' => 'nullable|in:formative,summative_material,summative_final',
            'max_score' => 'nullable|integer|min:1',
            'grades' => 'required|array|min:1',
            'grades.*.student_id' => 'required|exists:sch_std_students,id',
            'grades.*.score' => 'required|numeric|min:0|max:100',
            'grades.*.notes' => 'nullable|string|max:255',
        ]);

        $assessment = null;
        if (!empty($validated['assessment_id'])) {
            $assessment = Assessment::findOrFail($validated['assessment_id']);
        } else {
            $teacher = Staff::where('school_id', $schoolId)->first();
            $teacherId = $validated['teacher_id'] ?? ($teacher?->id ?? 1);

            $assessment = Assessment::create([
                'school_id' => $schoolId,
                'academic_year_id' => $validated['academic_year_id'] ?? 1,
                'semester_id' => $validated['semester_id'] ?? 1,
                'study_group_id' => $validated['study_group_id'],
                'subject_id' => $validated['subject_id'],
                'teacher_id' => $teacherId,
                'title' => $validated['assessment_title'],
                'type' => $validated['type'] ?? 'summative_material',
                'weight' => 2,
                'max_score' => $validated['max_score'] ?? 100,
                'date' => Carbon::now()->toDateString(),
                'description' => 'Nilai CBT diimpor otomatis dari ja-katampi',
            ]);
        }

        $syncedCount = 0;
        foreach ($validated['grades'] as $g) {
            Grade::updateOrCreate(
                [
                    'assessment_id' => $assessment->id,
                    'student_id' => $g['student_id'],
                ],
                [
                    'score' => $g['score'],
                    'notes' => $g['notes'] ?? 'Sinkronisasi CBT ja-katampi',
                ]
            );
            $syncedCount++;
        }

        // Update config stats
        $config = KatampiConfig::firstOrCreate(['school_id' => $schoolId]);
        $stats = $config->sync_stats ?? ['total_exams' => 0, 'total_grades_synced' => 0];
        $stats['total_exams'] = ($stats['total_exams'] ?? 0) + 1;
        $stats['total_grades_synced'] = ($stats['total_grades_synced'] ?? 0) + $syncedCount;

        $config->update([
            'last_sync_at' => Carbon::now(),
            'sync_stats' => $stats,
        ]);

        return $this->sendResponse([
            'assessment_id' => $assessment->id,
            'assessment_title' => $assessment->title,
            'grades_synced' => $syncedCount,
            'last_sync_at' => $config->last_sync_at,
        ], 'KataMPI CBT grades synchronized successfully');
    }
}
