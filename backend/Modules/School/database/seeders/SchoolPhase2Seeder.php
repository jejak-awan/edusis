<?php

declare(strict_types=1);

namespace Modules\School\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\School\Models\Academic\AcademicYear;
use Modules\School\Models\Academic\Attendance;
use Modules\School\Models\Academic\Schedule;
use Modules\School\Models\Academic\StudyGroup;
use Modules\School\Models\Academic\Subject;
use Modules\School\Models\Academic\TeachingJournal;
use Modules\School\Models\HR\Staff;
use Modules\School\Models\HR\StaffAttendance;
use Modules\School\Models\HR\StaffShift;
use Modules\School\Models\Institution\School;
use Modules\School\Models\Student\Student;

class SchoolPhase2Seeder extends Seeder
{
    public function run(): void
    {
        $school = School::first();
        if (!$school) {
            return;
        }

        // 1. Shift Kerja Guru & Staff
        $shiftReguler = StaffShift::firstOrCreate(
            ['school_id' => $school->id, 'name' => 'Shift Reguler Guru & Tendik'],
            [
                'start_time' => '07:00:00',
                'end_time' => '15:30:00',
                'grace_period_minutes' => 15,
                'is_active' => true,
            ]
        );

        // 2. Data Guru & Tenaga Kependidikan
        $staffData = [
            [
                'nip' => '196805121993031005',
                'nuptk' => '8435746648200002',
                'nik' => '3210121205680001',
                'full_name' => 'Udin Sutardi',
                'title_prefix' => 'Drs. H.',
                'title_suffix' => 'M.Pd.',
                'gender' => 'L',
                'employment_status' => 'PNS',
                'role_title' => 'Kepala Sekolah',
                'last_education' => 'S2',
                'major' => 'Manajemen Pendidikan',
                'certification_status' => true,
                'phone' => '081223344556',
                'email' => 'kepsek@smkn1talaga.sch.id',
                'address' => 'Talaga Kulon RT 01 RW 01',
            ],
            [
                'nip' => '198504202022211008',
                'nuptk' => '5432763665130082',
                'nik' => '3210122004850003',
                'full_name' => 'Budi Hartono',
                'title_prefix' => null,
                'title_suffix' => 'S.Kom.',
                'gender' => 'L',
                'employment_status' => 'PPPK',
                'role_title' => 'Guru Produktif PPLG',
                'last_education' => 'S1',
                'major' => 'Teknik Informatika',
                'certification_status' => true,
                'phone' => '082122334455',
                'email' => 'budi.hartono@smkn1talaga.sch.id',
                'address' => 'Kecamatan Cikijing',
            ],
            [
                'nip' => null,
                'nuptk' => '9876543210987654',
                'nik' => '3210125508890002',
                'full_name' => 'Siti Rahmawati',
                'title_prefix' => null,
                'title_suffix' => 'S.Pd.',
                'gender' => 'P',
                'employment_status' => 'GTY',
                'role_title' => 'Guru Matematika',
                'last_education' => 'S1',
                'major' => 'Pendidikan Matematika',
                'certification_status' => false,
                'phone' => '085233445566',
                'email' => 'siti.rahmawati@smkn1talaga.sch.id',
                'address' => 'Kecamatan Bantarujeg',
            ],
            [
                'nip' => '199008152019031009',
                'nuptk' => '1234567890123456',
                'nik' => '3210121508900004',
                'full_name' => 'Rian Kurniawan',
                'title_prefix' => null,
                'title_suffix' => 'S.Pd.',
                'gender' => 'L',
                'employment_status' => 'PNS',
                'role_title' => 'Guru Bahasa Inggris',
                'last_education' => 'S1',
                'major' => 'Pendidikan Bahasa Inggris',
                'certification_status' => true,
                'phone' => '087811223344',
                'email' => 'rian.kurniawan@smkn1talaga.sch.id',
                'address' => 'Kecamatan Talaga',
            ],
            [
                'nip' => '198811122015042001',
                'nuptk' => '6543210987654321',
                'nik' => '3210125211880005',
                'full_name' => 'Nurhayati',
                'title_prefix' => null,
                'title_suffix' => 'S.Psi.',
                'gender' => 'P',
                'employment_status' => 'PNS',
                'role_title' => 'Guru Bimbingan Konseling (BK)',
                'last_education' => 'S1',
                'major' => 'Psikologi Pendidikan',
                'certification_status' => true,
                'phone' => '081399887766',
                'email' => 'nurhayati.bk@smkn1talaga.sch.id',
                'address' => 'Kecamatan Talaga',
            ],
        ];

        $createdStaff = [];
        foreach ($staffData as $s) {
            $createdStaff[$s['full_name']] = Staff::firstOrCreate(
                ['school_id' => $school->id, 'full_name' => $s['full_name']],
                $s
            );
        }

        // 3. Staff Attendance Sample
        $budi = $createdStaff['Budi Hartono'];
        StaffAttendance::firstOrCreate(
            ['staff_id' => $budi->id, 'date' => now()->toDateString()],
            [
                'shift_id' => $shiftReguler->id,
                'check_in' => '06:48:22',
                'status' => 'hadir',
                'late_minutes' => 0,
                'notes' => 'Presensi mesin biometric / web',
            ]
        );

        // 4. Academic Context & Schedules
        $activeYear = AcademicYear::where('school_id', $school->id)->where('is_active', true)->first();
        $activeSem = $activeYear ? $activeYear->activeSemester : null;
        $rombel = StudyGroup::where('school_id', $school->id)->where('name', 'X PPLG 1')->first();
        $mapelWeb = Subject::where('school_id', $school->id)->where('code', 'PPLG-01')->first();
        $mapelMtk = Subject::where('school_id', $school->id)->where('code', 'UMUM-04')->first();

        if ($activeYear && $activeSem && $rombel && $mapelWeb && $budi) {
            // Schedule Senin: Pemrograman Web (Budi Hartono)
            $schedule1 = Schedule::firstOrCreate(
                [
                    'school_id' => $school->id,
                    'academic_year_id' => $activeYear->id,
                    'semester_id' => $activeSem->id,
                    'study_group_id' => $rombel->id,
                    'subject_id' => $mapelWeb->id,
                    'day_of_week' => 1, // Senin
                ],
                [
                    'teacher_id' => $budi->id,
                    'start_time' => '07:30:00',
                    'end_time' => '10:00:00',
                    'room_name' => 'Lab Rekayasa Perangkat Lunak 1',
                    'is_active' => true,
                ]
            );

            // 5. Teaching Journal Sample
            TeachingJournal::firstOrCreate(
                [
                    'school_id' => $school->id,
                    'study_group_id' => $rombel->id,
                    'subject_id' => $mapelWeb->id,
                    'teacher_id' => $budi->id,
                    'date' => now()->toDateString(),
                ],
                [
                    'academic_year_id' => $activeYear->id,
                    'semester_id' => $activeSem->id,
                    'schedule_id' => $schedule1->id,
                    'teaching_hour' => 'Jam 1 - 3',
                    'topic' => 'Arsitektur Web Modern dan RESTful API dengan Laravel 12',
                    'competency_description' => 'Memahami struktur endpoint API, autentikasi Sanctum, dan Eloquent ORM.',
                    'learning_activity' => 'Praktik langsung pembuatan modul sistem sekolah terisolasi.',
                    'present_count' => 3,
                    'sick_count' => 0,
                    'permit_count' => 0,
                    'absent_count' => 0,
                    'notes_reflection' => 'Seluruh siswa antusias dan berhasil memahami konsep routing API.',
                ]
            );

            // 6. Student Daily Attendances Sample
            $students = Student::where('school_id', $school->id)->get();
            foreach ($students as $std) {
                Attendance::firstOrCreate(
                    [
                        'student_id' => $std->id,
                        'date' => now()->toDateString(),
                    ],
                    [
                        'study_group_id' => $rombel->id,
                        'academic_year_id' => $activeYear->id,
                        'semester_id' => $activeSem->id,
                        'status' => 'H',
                        'notes' => 'Hadir tepat waktu',
                    ]
                );
            }
        }
    }
}
