<?php

declare(strict_types=1);

namespace Modules\School\Services\Operations;

use Illuminate\Support\Facades\DB;
use Modules\School\Models\Academic\AcademicYear;
use Modules\School\Models\Administration\PpdbRegistration;
use Modules\School\Models\Institution\School;
use Modules\School\Models\Student\Student;

class PpdbService
{
    /**
     * Daftarkan pendaftar calon siswa baru (PPDB).
     *
     * @param array<string, mixed> $data
     */
    public function registerApplicant(array $data, int $schoolId): PpdbRegistration
    {
        $activeYear = AcademicYear::where('school_id', $schoolId)->where('is_active', true)->first();
        $academicYearId = $activeYear ? $activeYear->id : 1;

        $year = (int) date('Y');
        $count = PpdbRegistration::where('school_id', $schoolId)->whereYear('created_at', $year)->count();
        $registrationNumber = sprintf('PPDB/%d/%04d', $year, $count + 1);

        return PpdbRegistration::create(array_merge($data, [
            'school_id' => $schoolId,
            'academic_year_id' => $academicYearId,
            'registration_number' => $registrationNumber,
            'status' => 'submitted',
            'average_report_score' => $data['average_report_score'] ?? 0,
        ]));
    }

    /**
     * Verifikasi berkas kelengkapan pendaftar PPDB.
     *
     * @param array<string, mixed> $data
     */
    public function verifyApplicant(PpdbRegistration $registration, array $data): PpdbRegistration
    {
        $registration->update([
            'status' => $data['status'],
            'verified_by' => $data['verified_by'] ?? $registration->verified_by,
            'verified_at' => now(),
            'notes' => $data['notes'] ?? $registration->notes,
        ]);

        return $registration;
    }

    /**
     * Terima pendaftar PPDB menjadi siswa aktif dan terbitkan NIS sekolah.
     *
     * @return array{registration: PpdbRegistration, student: Student}
     */
    public function admitApplicant(PpdbRegistration $registration): array
    {
        return DB::transaction(function () use ($registration) {
            $school = School::find($registration->school_id);
            $schoolLevelId = $school ? 1 : 1;

            $nis = sprintf('%s%04d', date('y'), $registration->id);

            $student = Student::create([
                'school_id' => $registration->school_id,
                'school_level_id' => $schoolLevelId,
                'department_id' => $registration->department_id,
                'nisn' => $registration->nisn,
                'nis' => $nis,
                'nik' => $registration->nik,
                'full_name' => $registration->full_name,
                'gender' => $registration->gender,
                'place_of_birth' => $registration->place_of_birth,
                'date_of_birth' => $registration->date_of_birth,
                'religion' => $registration->religion,
                'phone' => $registration->phone,
                'email' => $registration->email,
                'address' => $registration->address,
                'status' => 'active',
                'admission_date' => now()->toDateString(),
            ]);

            $registration->update([
                'status' => 'accepted',
                'notes' => ($registration->notes ? $registration->notes.' | ' : '').'Diterima menjadi siswa aktif (NIS: '.$nis.')',
            ]);

            return [
                'registration' => $registration,
                'student' => $student,
            ];
        });
    }
}
