<?php

declare(strict_types=1);

namespace Modules\School\Services\Student;

use Illuminate\Support\Facades\DB;
use Modules\School\Models\Academic\AcademicYear;
use Modules\School\Models\Counseling\CounselingCase;

class CounselingService
{
    /**
     * Buat kasus bimbingan konseling baru dengan nomor kasus terstandarisasi.
     *
     * @param array<string, mixed> $data
     */
    public function createCase(array $data, int $schoolId): CounselingCase
    {
        $activeYear = AcademicYear::where('school_id', $schoolId)
            ->where('is_active', true)
            ->with('activeSemester')
            ->first();
        assert($activeYear instanceof AcademicYear || $activeYear === null);

        $academicYearId = $activeYear ? $activeYear->id : 1;
        $semesterId = ($activeYear && $activeYear->activeSemester) ? $activeYear->activeSemester->id : 1;

        return DB::transaction(function () use ($data, $schoolId, $academicYearId, $semesterId): CounselingCase {
            $countToday = CounselingCase::where('school_id', $schoolId)
                ->whereDate('created_at', now()->toDateString())
                ->count();

            $caseNumber = sprintf('BK/%s/%03d', now()->format('Y/m'), $countToday + 1);
            $status = isset($data['status']) && is_string($data['status']) ? $data['status'] : 'open';

            $case = CounselingCase::create(array_merge($data, [
                'school_id' => $schoolId,
                'academic_year_id' => $academicYearId,
                'semester_id' => $semesterId,
                'case_number' => $caseNumber,
                'status' => $status,
            ]));
            assert($case instanceof CounselingCase);

            return $case;
        });
    }

    /**
     * Perbarui berkas penanganan bimbingan konseling.
     *
     * @param array<string, mixed> $data
     */
    public function updateCase(CounselingCase $case, array $data): CounselingCase
    {
        $case->update($data);

        return $case;
    }

    /**
     * Tutup dan tandai kasus bimbingan konseling telah terselesaikan.
     *
     * @param array<string, mixed> $data
     */
    public function resolveCase(CounselingCase $case, array $data): CounselingCase
    {
        $case->update(array_merge($data, [
            'status' => 'resolved',
        ]));

        return $case;
    }
}
