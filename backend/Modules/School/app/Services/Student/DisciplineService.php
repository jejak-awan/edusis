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
    private function asInt(mixed $val, int $default = 0): int
    {
        return is_numeric($val) ? (int) $val : $default;
    }

    private function asString(mixed $val, string $default = ''): string
    {
        return is_scalar($val) ? (string) $val : $default;
    }

    private function asNullableString(mixed $val): ?string
    {
        return is_scalar($val) ? (string) $val : null;
    }

    /**
     * Catat mutasi poin kredit (pelanggaran atau prestasi) siswa.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function recordPoint(array $data, string|int|null $reporterId = null): array
    {
        $category = PointCategory::findOrFail($this->asInt($data['category_id'] ?? null));
        assert($category instanceof PointCategory);

        $student = Student::findOrFail($this->asInt($data['student_id'] ?? null));
        assert($student instanceof Student);

        $points = isset($data['points']) ? $this->asInt($data['points']) : $category->default_points;

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
                'description' => $this->asString($data['description'] ?? ''),
                'date' => $data['date'] ?? now()->toDateString(),
                'reporter_id' => $reporterId,
                'status' => 'approved',
                'admin_notes' => isset($data['admin_notes']) ? $this->asNullableString($data['admin_notes']) : null,
            ]);
            assert($creditPoint instanceof CreditPoint);

            $violation = null;
            $achievement = null;

            if ($category->type === 'pelanggaran') {
                $spLevel = isset($data['sp_level']) ? $this->asString($data['sp_level'], 'none') : 'none';

                // Evaluasi ambang SP dari config jika belum ditetapkan manual
                if ($spLevel === 'none') {
                    $accumulated = $this->calculateAccumulatedViolationPoints($student->id);
                    /** @var array<string, int> $thresholds */
                    $thresholds = config('school.discipline.sp_thresholds', ['sp1' => 30, 'sp2' => 50, 'sp3' => 100]);
                    if ($accumulated >= ($thresholds['sp3'] ?? 100)) {
                        $spLevel = 'sp3';
                    } elseif ($accumulated >= ($thresholds['sp2'] ?? 50)) {
                        $spLevel = 'sp2';
                    } elseif ($accumulated >= ($thresholds['sp1'] ?? 30)) {
                        $spLevel = 'sp1';
                    }
                }

                $violation = Violation::create([
                    'student_id' => $student->id,
                    'credit_point_id' => $creditPoint->id,
                    'violation_date' => $data['date'] ?? now()->toDateString(),
                    'sanction' => isset($data['sanction']) ? $this->asNullableString($data['sanction']) : null,
                    'sp_level' => $spLevel,
                    'sp_letter_number' => isset($data['sp_letter_number']) ? $this->asNullableString($data['sp_letter_number']) : null,
                    'sp_issued_at' => ! empty($data['sp_letter_number']) ? now() : null,
                    'follow_up_notes' => isset($data['admin_notes']) ? $this->asNullableString($data['admin_notes']) : null,
                    'is_resolved' => false,
                ]);
            } else {
                $achievement = Achievement::create([
                    'student_id' => $student->id,
                    'credit_point_id' => $creditPoint->id,
                    'title' => isset($data['achievement_title']) ? $this->asString($data['achievement_title'], $category->name) : $category->name,
                    'level' => isset($data['achievement_level']) ? $this->asString($data['achievement_level'], 'sekolah') : 'sekolah',
                    'rank' => isset($data['achievement_rank']) ? $this->asNullableString($data['achievement_rank']) : null,
                    'type' => isset($data['achievement_type']) ? $this->asString($data['achievement_type'], 'akademik') : 'akademik',
                    'event_name' => isset($data['event_name']) ? $this->asNullableString($data['event_name']) : null,
                ]);
            }

            $freshStudent = $student->fresh();
            $currentScore = $freshStudent instanceof Student ? $freshStudent->total_credit_score : $student->total_credit_score;

            return [
                'credit_point' => $creditPoint->load('category'),
                'violation' => $violation,
                'achievement' => $achievement,
                'current_total_score' => $currentScore,
                'new_total_score' => $currentScore,
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
     *
     * @return array<string, mixed>
     */
    public function getStudentDisciplineSummary(int $studentId): array
    {
        $student = Student::findOrFail($studentId);
        assert($student instanceof Student);

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
