<?php

declare(strict_types=1);

namespace Modules\School\Services\Data;

use Illuminate\Support\Facades\DB;
use Modules\School\Models\HR\Staff;
use Modules\School\Models\Student\Student;
use Modules\School\Models\Student\StudentParent;

class ImportDataService
{
    /**
     * Parse and import array of student records from Excel / Dapodik export.
     *
     * @param  array<int, array<string, mixed>>  $rows
     * @return array{imported: int, errors: array<int, string>}
     */
    public function importStudents(array $rows, int $schoolId): array
    {
        $imported = 0;
        $errors = [];

        DB::transaction(function () use ($rows, $schoolId, &$imported, &$errors) {
            foreach ($rows as $index => $row) {
                $rowNum = $index + 2; // header offset

                if (empty($row['full_name']) && empty($row['nama_lengkap'])) {
                    $errors[] = "Baris #{$rowNum}: Nama lengkap siswa wajib diisi.";

                    continue;
                }

                $fullName = (string) ($row['full_name'] ?? $row['nama_lengkap']);
                $gender = strtoupper(trim((string) ($row['gender'] ?? $row['jenis_kelamin'] ?? 'L')));
                if (! in_array($gender, ['L', 'P'], true)) {
                    $gender = 'L';
                }

                $nisn = ! empty($row['nisn']) ? substr(trim((string) $row['nisn']), 0, 10) : null;
                $nis = ! empty($row['nis']) ? substr(trim((string) $row['nis']), 0, 20) : null;
                $nik = ! empty($row['nik']) ? substr(trim((string) $row['nik']), 0, 16) : null;

                $student = Student::updateOrCreate(
                    [
                        'school_id' => $schoolId,
                        'full_name' => $fullName,
                        'nisn' => $nisn,
                    ],
                    [
                        'nis' => $nis,
                        'nik' => $nik,
                        'gender' => $gender,
                        'place_of_birth' => $row['place_of_birth'] ?? $row['tempat_lahir'] ?? null,
                        'date_of_birth' => $row['date_of_birth'] ?? $row['tanggal_lahir'] ?? null,
                        'religion' => $row['religion'] ?? $row['agama'] ?? null,
                        'address' => $row['address'] ?? $row['alamat_lengkap'] ?? null,
                        'phone' => $row['phone'] ?? $row['telepon'] ?? null,
                        'email' => $row['email'] ?? null,
                        'status' => 'active',
                    ]
                );

                // Parent data if present
                if (! empty($row['father_name']) || ! empty($row['nama_ayah']) || ! empty($row['mother_name']) || ! empty($row['nama_ibu'])) {
                    StudentParent::updateOrCreate(
                        ['student_id' => $student->id],
                        [
                            'father_name' => $row['father_name'] ?? $row['nama_ayah'] ?? null,
                            'father_nik' => $row['father_nik'] ?? null,
                            'father_occupation' => $row['father_job'] ?? $row['pekerjaan_ayah'] ?? null,
                            'father_phone' => $row['father_phone'] ?? $row['telepon_ayah'] ?? null,
                            'mother_name' => $row['mother_name'] ?? $row['nama_ibu'] ?? null,
                            'mother_nik' => $row['mother_nik'] ?? null,
                            'mother_occupation' => $row['mother_job'] ?? $row['pekerjaan_ibu'] ?? null,
                            'mother_phone' => $row['mother_phone'] ?? $row['telepon_ibu'] ?? null,
                        ]
                    );
                }

                $imported++;
            }
        });

        return [
            'imported' => $imported,
            'errors' => $errors,
        ];
    }

    /**
     * Parse and import array of staff (GTK) records from Excel / Dapodik export.
     *
     * @param  array<int, array<string, mixed>>  $rows
     * @return array{imported: int, errors: array<int, string>}
     */
    public function importStaff(array $rows, int $schoolId): array
    {
        $imported = 0;
        $errors = [];

        DB::transaction(function () use ($rows, $schoolId, &$imported, &$errors) {
            foreach ($rows as $index => $row) {
                $rowNum = $index + 2;

                if (empty($row['full_name']) && empty($row['nama_lengkap'])) {
                    $errors[] = "Baris #{$rowNum}: Nama pegawai/guru wajib diisi.";

                    continue;
                }

                $fullName = (string) ($row['full_name'] ?? $row['nama_lengkap']);
                $gender = strtoupper(trim((string) ($row['gender'] ?? $row['jenis_kelamin'] ?? 'L')));
                if (! in_array($gender, ['L', 'P'], true)) {
                    $gender = 'L';
                }

                Staff::updateOrCreate(
                    [
                        'school_id' => $schoolId,
                        'full_name' => $fullName,
                    ],
                    [
                        'gender' => $gender,
                        'nip' => $row['nip'] ?? null,
                        'nuptk' => $row['nuptk'] ?? null,
                        'nik' => $row['nik'] ?? null,
                        'title_prefix' => $row['title_prefix'] ?? null,
                        'title_suffix' => $row['title_suffix'] ?? null,
                        'employment_status' => $row['employment_status'] ?? $row['status_kepegawaian'] ?? 'GTY',
                        'role_title' => $row['role_title'] ?? $row['jabatan'] ?? 'Guru Mata Pelajaran',
                        'phone' => $row['phone'] ?? $row['telepon'] ?? null,
                        'email' => $row['email'] ?? null,
                        'is_active' => true,
                    ]
                );

                $imported++;
            }
        });

        return [
            'imported' => $imported,
            'errors' => $errors,
        ];
    }
}
