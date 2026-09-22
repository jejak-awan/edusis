<?php

declare(strict_types=1);

namespace Modules\School\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\School\Models\Academic\AcademicYear;
use Modules\School\Models\Academic\Department;
use Modules\School\Models\Academic\Semester;
use Modules\School\Models\Academic\StudyGroup;
use Modules\School\Models\Academic\StudyGroupMember;
use Modules\School\Models\Academic\Subject;
use Modules\School\Models\Institution\School;
use Modules\School\Models\Institution\SchoolLevel;
use Modules\School\Models\Student\PointCategory;
use Modules\School\Models\Student\Student;
use Modules\School\Models\Student\StudentParent;

class SchoolPhase1Seeder extends Seeder
{
    public function run(): void
    {
        // 1. School Institution
        $school = School::firstOrCreate(
            ['npsn' => '20246452'],
            [
                'name' => 'SMK Negeri 1 Talaga',
                'type' => 'negeri',
                'nss' => '321021603001',
                'accreditation' => 'A',
                'curriculum' => 'Kurikulum Merdeka',
                'address' => 'Jl. Sekolah No. 1, Talaga Kulon',
                'kecamatan' => 'Talaga',
                'kabupaten_kota' => 'Kabupaten Majalengka',
                'provinsi' => 'Jawa Barat',
                'postal_code' => '45463',
                'phone' => '(0233) 891234',
                'email' => 'info@example-school.sch.id',
                'website' => 'https://example-school.sch.id',
                'principal_name' => 'Drs. H. Udin Sutardi, M.Pd.',
                'principal_nip' => '196805121993031005',
                'vision' => 'Terwujudnya lulusan yang beriman, bertakwa, unggul dalam teknologi, berkarakter Profil Pelajar Pancasila, dan kompetitif global.',
                'mission' => 'Menyelenggarakan pendidikan kejuruan berstandar industri dan berbasis teknologi digital.',
                'is_active' => true,
            ]
        );

        // 2. School Levels
        $level10 = SchoolLevel::firstOrCreate(['school_id' => $school->id, 'grade_level' => 10], ['name' => 'Kelas X (Sepuluh)', 'is_active' => true]);
        $level11 = SchoolLevel::firstOrCreate(['school_id' => $school->id, 'grade_level' => 11], ['name' => 'Kelas XI (Sebelas)', 'is_active' => true]);
        $level12 = SchoolLevel::firstOrCreate(['school_id' => $school->id, 'grade_level' => 12], ['name' => 'Kelas XII (Dua Belas)', 'is_active' => true]);

        // 3. Academic Year & Semesters
        $academicYear = AcademicYear::firstOrCreate(
            ['school_id' => $school->id, 'name' => '2026/2027'],
            [
                'start_date' => '2026-07-13',
                'end_date' => '2027-06-25',
                'is_active' => true,
            ]
        );

        Semester::firstOrCreate(
            ['academic_year_id' => $academicYear->id, 'semester_number' => 1],
            ['name' => 'Semester Ganjil', 'start_date' => '2026-07-13', 'end_date' => '2026-12-18', 'is_active' => true]
        );
        Semester::firstOrCreate(
            ['academic_year_id' => $academicYear->id, 'semester_number' => 2],
            ['name' => 'Semester Genap', 'start_date' => '2027-01-04', 'end_date' => '2027-06-25', 'is_active' => false]
        );

        // 4. Departments
        $deptPplg = Department::firstOrCreate(
            ['school_id' => $school->id, 'code' => 'PPLG'],
            ['name' => 'Pengembangan Perangkat Lunak dan Gim', 'description' => 'Kompetensi keahlian rekayasa piranti lunak, web, mobile, dan game.', 'is_active' => true]
        );
        $deptTjkt = Department::firstOrCreate(
            ['school_id' => $school->id, 'code' => 'TJKT'],
            ['name' => 'Teknik Jaringan Komputer dan Telekomunikasi', 'description' => 'Infrastruktur cloud, router, switching, dan keamanan jaringan.', 'is_active' => true]
        );
        $deptDkv = Department::firstOrCreate(
            ['school_id' => $school->id, 'code' => 'DKV'],
            ['name' => 'Desain Komunikasi Visual', 'description' => 'Multimedia interaktif, video editing, branding dan UI/UX design.', 'is_active' => true]
        );

        // 5. Subjects
        $subjects = [
            ['code' => 'UMUM-01', 'name' => 'Pendidikan Agama dan Budi Pekerti', 'group' => 'Umum', 'kkm' => 75],
            ['code' => 'UMUM-02', 'name' => 'Pendidikan Pancasila', 'group' => 'Umum', 'kkm' => 75],
            ['code' => 'UMUM-03', 'name' => 'Bahasa Indonesia', 'group' => 'Umum', 'kkm' => 75],
            ['code' => 'UMUM-04', 'name' => 'Matematika Terapan', 'group' => 'Umum', 'kkm' => 70],
            ['code' => 'UMUM-05', 'name' => 'Bahasa Inggris Kejuruan', 'group' => 'Umum', 'kkm' => 70],
            ['code' => 'PPLG-01', 'name' => 'Pemrograman Web dan Perangkat Bergerak', 'group' => 'Kejuruan', 'kkm' => 78],
            ['code' => 'PPLG-02', 'name' => 'Basis Data Relasional & NoSQL', 'group' => 'Kejuruan', 'kkm' => 78],
            ['code' => 'PPLG-03', 'name' => 'Pemrograman Berorientasi Objek', 'group' => 'Kejuruan', 'kkm' => 78],
        ];

        foreach ($subjects as $item) {
            Subject::firstOrCreate(
                ['school_id' => $school->id, 'code' => $item['code']],
                $item
            );
        }

        // 6. Study Group (Rombel)
        $rombelX = StudyGroup::firstOrCreate(
            ['school_id' => $school->id, 'academic_year_id' => $academicYear->id, 'name' => 'X PPLG 1'],
            [
                'school_level_id' => $level10->id,
                'department_id' => $deptPplg->id,
                'capacity' => 36,
                'is_active' => true,
            ]
        );

        // 7. Point Categories (SISKA Disciplinary System)
        $pointCategories = [
            // Pelanggaran (Negatif)
            ['name' => 'Terlambat Masuk Sekolah (< 15 menit)', 'type' => 'pelanggaran', 'default_points' => -5, 'description' => 'Siswa datang setelah bel masuk berbunyi.'],
            ['name' => 'Tidak Menggunakan Atribut Seragam Lengkap', 'type' => 'pelanggaran', 'default_points' => -5, 'description' => 'Tidak memakai dasi, badge, ikat pinggang sekolah atau sepatu hitam.'],
            ['name' => 'Membolos / Meninggalkan Jam Pelajaran Tanpa Izin', 'type' => 'pelanggaran', 'default_points' => -15, 'description' => 'Tidak berada di kelas saat KBM berlangsung tanpa surat izin guru piket.'],
            ['name' => 'Merokok / Vape di Lingkungan Sekolah', 'type' => 'pelanggaran', 'default_points' => -30, 'description' => 'Membawa atau menggunakan rokok/elektronik di radius sekolah.'],
            ['name' => 'Tindakan Bullying / Perundungan Fisik / Verbal', 'type' => 'pelanggaran', 'default_points' => -50, 'description' => 'Melakukan intimidasi atau pelecehan kepada sesama siswa.'],
            ['name' => 'Berkelahi / Terlibat Tawuran Antar Pelajar', 'type' => 'pelanggaran', 'default_points' => -75, 'description' => 'Tindakan kekerasan fisik yang mencoreng nama baik sekolah.'],

            // Prestasi (Positif)
            ['name' => 'Juara 1 Lomba / Kompetisi Tingkat Kabupaten', 'type' => 'prestasi', 'default_points' => 25, 'description' => 'Mendapatkan peringkat pertama pada ajang resmi tingkat kabupaten/kota.'],
            ['name' => 'Juara LKS / O2SN / FLS2N Tingkat Provinsi', 'type' => 'prestasi', 'default_points' => 50, 'description' => 'Meraih medali atau juara pada kompetisi vokasi/olahraga/seni provinsi.'],
            ['name' => 'Peraih Medali / Prestasi Tingkat Nasional', 'type' => 'prestasi', 'default_points' => 100, 'description' => 'Mengharumkan nama sekolah di tingkat nasional.'],
            ['name' => 'Pengurus Aktif OSIS / MPK / Ekstrakurikuler', 'type' => 'prestasi', 'default_points' => 15, 'description' => 'Berdedikasi dalam kepengurusan organisasi siswa.'],
            ['name' => 'Aksi Teladan Kejujuran & Sosial Berdampak', 'type' => 'prestasi', 'default_points' => 20, 'description' => 'Mengembalikan barang temuan berharga atau aksi kemanusiaan terpuji.'],
        ];

        foreach ($pointCategories as $cat) {
            PointCategory::firstOrCreate(
                ['name' => $cat['name']],
                $cat
            );
        }

        // 8. Sample Students (Dapodik-compliant)
        $sampleStudents = [
            [
                'nisn' => '0081234567',
                'nis' => '26271001',
                'nik' => '3210121508080001',
                'full_name' => 'Ahmad Fauzi Ramadhan',
                'gender' => 'L',
                'place_of_birth' => 'Majalengka',
                'date_of_birth' => '2008-09-15',
                'religion' => 'Islam',
                'address' => 'Desa Talaga Wetan RT 02 RW 03',
                'kecamatan' => 'Talaga',
                'kabupaten_kota' => 'Majalengka',
                'provinsi' => 'Jawa Barat',
                'postal_code' => '45463',
                'phone' => '081234567890',
                'email' => 'ahmad.fauzi@siswa.edusis.local',
                'entry_year' => '2026',
                'status' => 'active',
                'parent' => [
                    'father_name' => 'H. Ramadhan Kusuma',
                    'father_nik' => '3210121004750002',
                    'father_birth_year' => 1975,
                    'father_education' => 'S1',
                    'father_occupation' => 'Wiraswasta',
                    'father_income' => 'Rp 5.000.000 - Rp 10.000.000',
                    'father_phone' => '081298765432',
                    'mother_name' => 'Hj. Siti Aminah',
                    'mother_nik' => '3210125506780001',
                    'mother_birth_year' => 1978,
                    'mother_education' => 'D3',
                    'mother_occupation' => 'Ibu Rumah Tangga',
                    'mother_income' => 'Tidak Berpenghasilan',
                    'mother_phone' => '081398765431',
                    'economic_status' => 'mampu',
                    'emergency_phone' => '081298765432',
                    'emergency_contact_name' => 'H. Ramadhan Kusuma (Ayah)',
                ],
            ],
            [
                'nisn' => '0087654321',
                'nis' => '26271002',
                'nik' => '3210126204080003',
                'full_name' => 'Dewi Lestari Putri',
                'gender' => 'P',
                'place_of_birth' => 'Bandung',
                'date_of_birth' => '2008-04-22',
                'religion' => 'Islam',
                'address' => 'Jl. Cikijing Raya No. 45',
                'kecamatan' => 'Cikijing',
                'kabupaten_kota' => 'Majalengka',
                'provinsi' => 'Jawa Barat',
                'postal_code' => '45466',
                'phone' => '082212345678',
                'email' => 'dewi.lestari@siswa.edusis.local',
                'entry_year' => '2026',
                'status' => 'active',
                'parent' => [
                    'father_name' => 'Budi Santoso',
                    'father_nik' => '3210121102720005',
                    'father_birth_year' => 1972,
                    'father_education' => 'SMA',
                    'father_occupation' => 'Petani / Pekebun',
                    'father_income' => 'Rp 2.000.000 - Rp 3.500.000',
                    'father_phone' => '082198765400',
                    'mother_name' => 'Enok Rohaeti',
                    'mother_nik' => '3210124803760002',
                    'mother_birth_year' => 1976,
                    'mother_education' => 'SMP',
                    'mother_occupation' => 'Pedagang',
                    'mother_income' => 'Rp 1.000.000 - Rp 2.000.000',
                    'mother_phone' => '082198765401',
                    'economic_status' => 'kurang_mampu',
                    'is_kip' => true,
                    'kip_number' => 'KIP-2026-3210-998',
                    'emergency_phone' => '082198765400',
                    'emergency_contact_name' => 'Budi Santoso (Ayah)',
                ],
            ],
            [
                'nisn' => '0089876543',
                'nis' => '26271003',
                'nik' => '3210121811080004',
                'full_name' => 'Rizki Muhammad Pratama',
                'gender' => 'L',
                'place_of_birth' => 'Majalengka',
                'date_of_birth' => '2008-11-18',
                'religion' => 'Islam',
                'address' => 'Dusun Mekarsari RT 01 RW 01',
                'kecamatan' => 'Bantarujeg',
                'kabupaten_kota' => 'Majalengka',
                'provinsi' => 'Jawa Barat',
                'postal_code' => '45464',
                'phone' => '085298765432',
                'email' => 'rizki.pratama@siswa.edusis.local',
                'entry_year' => '2026',
                'status' => 'active',
                'parent' => [
                    'father_name' => 'Asep Saepudin',
                    'father_nik' => '3210121409740003',
                    'father_birth_year' => 1974,
                    'father_education' => 'SMA',
                    'father_occupation' => 'Karyawan Swasta',
                    'father_income' => 'Rp 3.500.000 - Rp 5.000.000',
                    'father_phone' => '085211223344',
                    'mother_name' => 'Nunung Nurhayati',
                    'mother_nik' => '3210125211770001',
                    'mother_birth_year' => 1977,
                    'mother_education' => 'SMA',
                    'mother_occupation' => 'Ibu Rumah Tangga',
                    'mother_income' => 'Tidak Berpenghasilan',
                    'mother_phone' => '085211223345',
                    'economic_status' => 'mampu',
                    'emergency_phone' => '085211223344',
                    'emergency_contact_name' => 'Asep Saepudin (Ayah)',
                ],
            ],
        ];

        foreach ($sampleStudents as $sData) {
            $parentData = $sData['parent'];
            unset($sData['parent']);

            $student = Student::firstOrCreate(
                ['nisn' => $sData['nisn']],
                array_merge($sData, [
                    'school_id' => $school->id,
                    'school_level_id' => $level10->id,
                    'department_id' => $deptPplg->id,
                ])
            );

            StudentParent::firstOrCreate(
                ['student_id' => $student->id],
                $parentData
            );

            StudyGroupMember::firstOrCreate(
                ['study_group_id' => $rombelX->id, 'student_id' => $student->id],
                ['status' => 'active']
            );
        }
    }
}
