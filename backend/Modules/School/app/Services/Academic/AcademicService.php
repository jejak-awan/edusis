<?php

declare(strict_types=1);

namespace Modules\School\Services\Academic;

use Illuminate\Support\Facades\DB;
use Modules\School\Models\Academic\AcademicYear;
use Modules\School\Models\Academic\Schedule;
use Modules\School\Models\Academic\Semester;
use Modules\School\Models\Academic\TeachingJournal;

class AcademicService
{
    /**
     * Buat tahun ajaran baru dan otomatis inisialisasi Semester Ganjil & Genap.
     */
    public function createAcademicYear(array $data, int $schoolId): AcademicYear
    {
        return DB::transaction(function () use ($data, $schoolId) {
            if (!empty($data['is_active'])) {
                AcademicYear::where('school_id', $schoolId)->update(['is_active' => false]);
            }

            $year = AcademicYear::create(array_merge($data, [
                'school_id' => $schoolId,
            ]));

            Semester::create([
                'academic_year_id' => $year->id,
                'name' => 'Semester Ganjil',
                'semester_number' => 1,
                'is_active' => (bool) ($year->is_active ?? false),
            ]);

            Semester::create([
                'academic_year_id' => $year->id,
                'name' => 'Semester Genap',
                'semester_number' => 2,
                'is_active' => false,
            ]);

            return $year->load('semesters');
        });
    }

    /**
     * Aktifkan tahun ajaran tertentu dan nonaktifkan yang lain dalam satu sekolah.
     */
    public function activateAcademicYear(int $yearId, int $schoolId): AcademicYear
    {
        return DB::transaction(function () use ($yearId, $schoolId) {
            AcademicYear::where('school_id', $schoolId)->update(['is_active' => false]);

            $year = AcademicYear::where('school_id', $schoolId)->findOrFail($yearId);
            $year->update(['is_active' => true]);

            return $year;
        });
    }

    /**
     * Dapatkan tahun ajaran aktif untuk institusi sekolah.
     */
    public function getActiveAcademicYear(int $schoolId): ?AcademicYear
    {
        return AcademicYear::where('school_id', $schoolId)
            ->where('is_active', true)
            ->with('activeSemester')
            ->first();
    }

    /**
     * Aktifkan semester tertentu dalam tahun ajaran.
     */
    public function activateSemester(int $semesterId): Semester
    {
        return DB::transaction(function () use ($semesterId) {
            $semester = Semester::findOrFail($semesterId);
            Semester::where('academic_year_id', $semester->academic_year_id)->update(['is_active' => false]);
            $semester->update(['is_active' => true]);

            return $semester;
        });
    }

    /**
     * Simpan jadwal KBM baru.
     */
    public function createSchedule(array $data, int $schoolId): Schedule
    {
        $activeYear = $this->getActiveAcademicYear($schoolId);
        $academicYearId = $activeYear?->id ?? 1;

        return Schedule::create(array_merge($data, [
            'school_id' => $schoolId,
            'academic_year_id' => $academicYearId,
        ]));
    }

    /**
     * Simpan catatan jurnal mengajar guru.
     */
    public function recordTeachingJournal(array $data, int $schoolId): TeachingJournal
    {
        $activeYear = $this->getActiveAcademicYear($schoolId);
        $academicYearId = $activeYear?->id ?? 1;
        $semesterId = $activeYear?->activeSemester?->id ?? 1;

        return TeachingJournal::create(array_merge($data, [
            'school_id' => $schoolId,
            'academic_year_id' => $academicYearId,
            'semester_id' => $semesterId,
        ]));
    }
}
