<?php

declare(strict_types=1);

namespace Modules\School\Services\Student;

use Illuminate\Support\Facades\DB;
use Modules\School\Models\Academic\StudyGroupMember;
use Modules\School\Models\Student\Student;
use Modules\School\Models\Student\StudentParent;

class StudentService
{
    /**
     * Daftarkan siswa baru beserta biodata orang tua / wali dan rombel awal.
     *
     * @param array<string, mixed> $data
     */
    public function registerStudent(array $data, int $schoolId): Student
    {
        $studentData = $data;
        $rawParent = $studentData['parent'] ?? [];
        /** @var array<string, mixed> $parentData */
        $parentData = is_array($rawParent) ? $rawParent : [];
        $studyGroupId = $studentData['study_group_id'] ?? null;
        unset($studentData['parent'], $studentData['study_group_id']);

        $studentData['school_id'] = $schoolId;

        return DB::transaction(function () use ($studentData, $parentData, $studyGroupId) {
            $student = Student::create($studentData);

            if (! empty($parentData)) {
                $parentData['student_id'] = $student->id;
                StudentParent::create($parentData);
            }

            if ($studyGroupId) {
                StudyGroupMember::create([
                    'study_group_id' => $studyGroupId,
                    'student_id' => $student->id,
                    'status' => 'active',
                ]);
            }

            return $student->load(['level', 'department', 'studyGroups', 'parent']);
        });
    }

    /**
     * Perbarui data pokok siswa dan data orang tua.
     *
     * @param array<string, mixed> $data
     */
    public function updateStudent(Student $student, array $data): Student
    {
        $studentData = $data;
        $rawParent = $studentData['parent'] ?? null;
        /** @var array<string, mixed>|null $parentData */
        $parentData = is_array($rawParent) ? $rawParent : null;
        unset($studentData['parent']);

        return DB::transaction(function () use ($student, $studentData, $parentData) {
            $student->update($studentData);

            if ($parentData !== null) {
                StudentParent::updateOrCreate(
                    ['student_id' => $student->id],
                    $parentData
                );
            }

            return $student->load(['level', 'department', 'studyGroups', 'parent']);
        });
    }

    /**
     * Dapatkan profil lengkap siswa beserta rekam jejak poin kredit, pelanggaran, dan prestasi.
     *
     * @return array<string, mixed>
     */
    public function getStudentProfile(int $studentId): array
    {
        $student = Student::with([
            'level',
            'department',
            'studyGroups',
            'parent',
            'creditPoints' => function ($q) {
                $q->with('category')->latest()->limit(20);
            },
            'violations' => function ($q) {
                $q->latest()->limit(10);
            },
            'achievements' => function ($q) {
                $q->latest()->limit(10);
            },
        ])->findOrFail($studentId);

        return [
            'student' => $student,
            'total_credit_score' => $student->total_credit_score,
        ];
    }
}
