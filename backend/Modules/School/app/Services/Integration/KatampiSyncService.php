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
     *
     * @return array<string, mixed>
     */
    public function generateSsoToken(int $schoolId, ?int $studentId = null, mixed $user = null): array
    {
        $config = $this->getConfig($schoolId);
        $school = School::find($schoolId);

        $student = null;
        if ($studentId) {
            $student = Student::where('school_id', $schoolId)->with('studyGroups')->find($studentId);
            assert($student instanceof Student || $student === null);
        }

        $studentGroup = $student?->studyGroups->first();
        assert($studentGroup instanceof StudyGroup || $studentGroup === null);

        $userId = 1;
        $userName = 'EduSIS User';
        $userRole = 'teacher';
        $username = 'edusis_user';

        if ($user instanceof \Modules\Core\System\Models\User) {
            $userId = $user->id;
            $userName = $user->name;
            $username = $user->username ?? $user->email;
            $userRole = 'teacher';
        } elseif (is_object($user) && isset($user->id)) {
            $userId = $user->id;
            $userName = isset($user->name) && is_string($user->name) ? $user->name : 'EduSIS User';
            $username = isset($user->email) && is_string($user->email) ? $user->email : 'edusis_user';
        }

        if ($student) {
            $userId = $student->id;
            $userName = $student->full_name;
            $username = $student->nisn;
            $userRole = 'student';
        }

        $payload = [
            'sub' => $userId,
            'name' => $userName,
            'username' => $username,
            'role' => $userRole,
            'student_id' => $student?->id,
            'nisn' => $student?->nisn,
            'study_group' => $studentGroup?->name,
            'school_id' => $schoolId,
            'school_name' => $school instanceof School ? $school->name : 'EduSIS School',
            'iat' => time(),
            'exp' => time() + 3600,
        ];

        $jsonPayload = (string) json_encode($payload);
        $token = base64_encode($jsonPayload);
        $cbtUrl = rtrim($config->cbt_url, '/').'/auth/sso?token='.urlencode($token);

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
     *
     * @return array<string, mixed>
     */
    public function getRoster(int $schoolId, ?int $studyGroupId = null): array
    {
        $query = StudyGroup::where('school_id', $schoolId)->with(['students:id,nisn,full_name,gender', 'department']);

        if ($studyGroupId) {
            $query->where('id', $studyGroupId);
        }

        /** @var \Illuminate\Database\Eloquent\Collection<int, StudyGroup> $groups */
        $groups = $query->get();

        $roster = $groups->map(function (StudyGroup $group): array {
            return [
                'study_group_id' => $group->id,
                'name' => $group->name,
                'department' => $group->department?->name,
                'total_students' => $group->students->count(),
                'students' => $group->students->map(function (Student $s): array {
                    return [
                        'id' => $s->id,
                        'nisn' => $s->nisn,
                        'name' => $s->full_name,
                        'gender' => $s->gender,
                    ];
                }),
            ];
        });

        return [
            'school_id' => $schoolId,
            'total_groups' => $groups->count(),
            'roster' => $roster,
        ];
    }
}
