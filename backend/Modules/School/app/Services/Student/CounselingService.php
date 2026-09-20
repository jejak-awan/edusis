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
     */
    public function createCase(array $data, int $schoolId): CounselingCase
    {
        $activeYear = AcademicYear::where('school_id', $schoolId)
            ->where('is_active', true)
            ->with('activeSemester')
            ->first();

        $academicYearId = $activeYear ? $activeYear->id : 1;
        $semesterId = $activeYear?->activeSemester?->id ?? 1;

        return DB::transaction(function () use ($data, $schoolId, $academicYearId, $semesterId) {
            $countToday = CounselingCase::where('school_id', $schoolId)
                ->whereDate('created_at', now()->toDateString())
                ->count();

            $caseNumber = sprintf('BK/%s/%03d', now()->format('Y/m'), $countToday + 1);

            return CounselingCase::create(array_merge($data, [
                'school_id' => $schoolId,
                'academic_year_id' => $academicYearId,
                'semester_id' => $semesterId,
                'case_number' => $caseNumber,
                'status' => $data['status'] ?? 'open',
            ]));
        });
    }

    /**
     * Perbarui berkas penanganan bimbingan konseling.
     */
    public function updateCase(CounselingCase $case, array $data): CounselingCase
    {
        $case->update($data);

        return $case;
    }

    /**
     * Tutup dan tandai kasus bimbingan konseling telah terselesaikan.
     */
    public function resolveCase(CounselingCase $case, array $data): CounselingCase
    {
        $case->update(array_merge($data, [
            'status' => 'resolved',
        ]));

        return $case;
    }
}
