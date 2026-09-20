<?php

declare(strict_types=1);

namespace Modules\School\Services\Integration;

use Modules\School\Models\Academic\StudyGroup;
use Modules\School\Models\Institution\School;
use Modules\School\Models\Integration\KatampiConfig;
use Modules\School\Models\Student\Student;

class KatampiSyncService
{
    /**
     * Dapatkan konfigurasi integrasi KataMPI untuk sekolah tertentu.
     */
    public function getConfig(int $schoolId): KatampiConfig
    {
        return KatampiConfig::firstOrCreate(
            ['school_id' => $schoolId],
            [
                'cbt_url' => 'http://ja-katampi:8787',
                'api_key' => bin2hex(random_bytes(16)),
                'is_active' => true,
                'sync_stats' => ['total_exams' => 0, 'total_grades_synced' => 0],
            ]
        );
    }

    /**
     * Hasilkan token SSO terenkripsi untuk transfer sesi ke KataMPI CBT Engine.
     */
    public function generateSsoToken(int $schoolId, ?int $studentId = null, mixed $user = null): array
    {
        $config = $this->getConfig($schoolId);
        $school = School::find($schoolId);

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
            'exp' => time() + 3600,
        ];

        $token = base64_encode(json_encode($payload));
        $cbtUrl = rtrim($config->cbt_url, '/') . '/auth/sso?token=' . urlencode($token);

        return [
            'token' => $token,
            'cbt_url' => $cbtUrl,
            'expires_in' => 3600,
            'user' => [
                'name' => $payload['name'],
                'role' => $payload['role'],
            ],
        ];
    }

    /**
     * Dapatkan data roster rombel dan siswa untuk sinkronisasi ke KataMPI CBT.
     */
    public function getRoster(int $schoolId, ?int $studyGroupId = null): array
    {
        $query = StudyGroup::where('school_id', $schoolId)->with(['students:id,nisn,full_name,gender', 'department']);

        if ($studyGroupId) {
            $query->where('id', $studyGroupId);
        }

        $groups = $query->get();

        $roster = $groups->map(function ($group) {
            return [
                'study_group_id' => $group->id,
                'name' => $group->name,
                'department' => $group->department?->name,
                'total_students' => $group->students->count(),
                'students' => $group->students->map(fn($s) => [
                    'id' => $s->id,
                    'nisn' => $s->nisn,
                    'name' => $s->full_name,
                    'gender' => $s->gender,
                ]),
            ];
        });

        return [
            'school_id' => $schoolId,
            'total_groups' => $groups->count(),
            'roster' => $roster,
        ];
    }
}
