<?php

declare(strict_types=1);

namespace Modules\School\Services\Student;

use Illuminate\Support\Facades\DB;
use Modules\School\Models\Academic\AcademicYear;
use Modules\School\Models\Student\Achievement;
use Modules\School\Models\Student\CreditPoint;
use Modules\School\Models\Student\PointCategory;
use Modules\School\Models\Student\Student;
use Modules\School\Models\Student\Violation;

class DisciplineService
{
    /**
     * Catat mutasi poin kredit (pelanggaran atau prestasi) siswa.
     */
    public function recordPoint(array $data, string|int|null $reporterId = null): array
    {
        $category = PointCategory::findOrFail($data['category_id']);
        $student = Student::findOrFail($data['student_id']);

        $points = $data['points'] ?? $category->default_points;

        $activeYear = AcademicYear::where('school_id', $student->school_id)
            ->where('is_active', true)
            ->with('activeSemester')
            ->first();

        $academicYearId = $activeYear ? $activeYear->id : null;
        $semesterId = $activeYear?->activeSemester?->id ?? null;

        return DB::transaction(function () use (
            $data,
            $student,
            $category,
            $points,
            $reporterId,
            $academicYearId,
            $semesterId
        ) {
            $creditPoint = CreditPoint::create([
                'student_id' => $student->id,
                'category_id' => $category->id,
                'academic_year_id' => $academicYearId,
                'semester_id' => $semesterId,
                'points' => $points,
                'description' => $data['description'],
                'date' => $data['date'],
                'reporter_id' => $reporterId,
                'status' => 'approved',
                'admin_notes' => $data['admin_notes'] ?? null,
            ]);

            $violation = null;
            $achievement = null;

            if ($category->type === 'pelanggaran') {
                $spLevel = $data['sp_level'] ?? 'none';

                // Evaluasi ambang SP dari config jika belum ditetapkan manual
                if ($spLevel === 'none') {
                    $accumulated = $this->calculateAccumulatedViolationPoints($student->id);
                    $thresholds = config('school.discipline.sp_thresholds', ['sp1' => 30, 'sp2' => 50, 'sp3' => 100]);
                    if ($accumulated >= $thresholds['sp3']) {
                        $spLevel = 'sp3';
                    } elseif ($accumulated >= $thresholds['sp2']) {
                        $spLevel = 'sp2';
                    } elseif ($accumulated >= $thresholds['sp1']) {
                        $spLevel = 'sp1';
                    }
                }

                $violation = Violation::create([
                    'student_id' => $student->id,
                    'credit_point_id' => $creditPoint->id,
                    'violation_date' => $data['date'],
                    'sanction' => $data['sanction'] ?? null,
                    'sp_level' => $spLevel,
                    'sp_letter_number' => $data['sp_letter_number'] ?? null,
                    'sp_issued_at' => !empty($data['sp_letter_number']) ? now() : null,
                    'follow_up_notes' => $data['admin_notes'] ?? null,
                    'is_resolved' => false,
                ]);
            } else {
                $achievement = Achievement::create([
                    'student_id' => $student->id,
                    'credit_point_id' => $creditPoint->id,
                    'title' => $data['achievement_title'] ?? $category->name,
                    'level' => $data['achievement_level'] ?? 'sekolah',
                    'rank' => $data['achievement_rank'] ?? null,
                    'type' => $data['achievement_type'] ?? 'akademik',
                    'event_name' => $data['event_name'] ?? null,
                ]);
            }

            return [
                'credit_point' => $creditPoint->load('category'),
                'violation' => $violation,
                'achievement' => $achievement,
                'current_total_score' => $student->fresh()->total_credit_score,
                'new_total_score' => $student->fresh()->total_credit_score,
            ];
        });
    }

    /**
     * Hitung total akumulasi poin pelanggaran siswa.
     */
    public function calculateAccumulatedViolationPoints(int $studentId): int
    {
        return (int) CreditPoint::where('student_id', $studentId)
            ->whereHas('category', fn ($q) => $q->where('type', 'pelanggaran'))
            ->sum('points');
    }

    /**
     * Dapatkan rekapitulasi poin dan riwayat disiplin siswa.
     */
    public function getStudentDisciplineSummary(int $studentId): array
    {
        $student = Student::findOrFail($studentId);

        $points = CreditPoint::where('student_id', $studentId)
            ->with(['category', 'reporter'])
            ->latest('date')
            ->get();

        $violations = Violation::where('student_id', $studentId)->latest('violation_date')->get();
        $achievements = Achievement::where('student_id', $studentId)->latest('id')->get();

        return [
            'student' => $student->only(['id', 'full_name', 'nisn', 'nis']),
            'total_credit_score' => $student->total_credit_score,
            'violations_count' => $violations->count(),
            'achievements_count' => $achievements->count(),
            'points_history' => $points,
            'violations' => $violations,
            'achievements' => $achievements,
        ];
    }
}
