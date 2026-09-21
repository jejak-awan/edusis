<?php

declare(strict_types=1);

namespace Modules\School\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\School\Models\Academic\AcademicYear;
use Modules\School\Models\Academic\Department;
use Modules\School\Models\Administration\GuestBook;
use Modules\School\Models\Administration\Letter;
use Modules\School\Models\Administration\PpdbRegistration;
use Modules\School\Models\HR\Staff;
use Modules\School\Models\Institution\School;
use Modules\School\Models\Student\Student;
use Modules\School\Models\StudentAffairs\Extracurricular;
use Modules\School\Models\StudentAffairs\OsisCandidate;
use Modules\School\Models\StudentAffairs\OsisElection;
use Modules\School\Models\StudentAffairs\OsisVote;

class SchoolPhase4Seeder extends Seeder
{
    public function run(): void
    {
        $school = School::first();
        if (! $school) {
            return;
        }

        $academicYear = AcademicYear::where('school_id', $school->id)->where('is_active', true)->first();
        $departments = Department::where('school_id', $school->id)->get();
        $teachers = Staff::where('school_id', $school->id)->get();
        $students = Student::where('school_id', $school->id)->get();

        $dept1 = $departments->first();
        $dept2 = $departments->count() > 1 ? $departments->get(1) : $dept1;
        $dept3 = $departments->count() > 2 ? $departments->get(2) : $dept1;
        $headmaster = $teachers->first();

        // 1. Letters (Surat Masuk & Keluar TU)
        Letter::firstOrCreate(
            ['letter_number' => '421.3/0892/Disdik-Jabar/IX/2026'],
            [
                'school_id' => $school->id,
                'type' => 'incoming',
                'reference_number' => 'DISDIK-JB/0892/2026',
                'category' => 'invitation',
                'sender' => 'Dinas Pendidikan Provinsi Jawa Barat',
                'recipient' => 'Kepala '.$school->name,
                'subject' => 'Undangan Rapat Koordinasi Kurikulum Merdeka dan Akreditasi Sekolah Vokasi',
                'letter_date' => now()->subDays(5)->toDateString(),
                'received_or_sent_date' => now()->subDays(4)->toDateString(),
                'status' => 'registered',
                'disposition_to' => 'Wakil Kepala Sekolah Bidang Kurikulum',
                'disposition_instruction' => 'Tindaklanjuti, siapkan bahan laporan kurikulum, dan hadiri tepat waktu.',
                'disposition_status' => 'processed',
                'notes' => 'Acara di Aula Gedung Sate hari Kamis pukul 09:00 WIB.',
            ]
        );

        Letter::firstOrCreate(
            ['letter_number' => 'TELKOM/DDS/IX/2026/041'],
            [
                'school_id' => $school->id,
                'type' => 'incoming',
                'reference_number' => 'HR-TELKOM/041',
                'category' => 'general',
                'sender' => 'PT Telkom Indonesia (Divisi Digital & Edukasi)',
                'recipient' => 'Kepala Program Keahlian PPLG & TJKT',
                'subject' => 'Pemberitahuan Kuota Program Praktik Kerja Lapangan (PKL) & Magang Industri',
                'letter_date' => now()->subDays(3)->toDateString(),
                'received_or_sent_date' => now()->subDays(2)->toDateString(),
                'status' => 'registered',
                'disposition_to' => 'Ketua Hubungan Industri (Hubin) & Kepala Bengkel',
                'disposition_instruction' => 'Seleksi siswa berprestasi yang memenuhi kualifikasi keahlian.',
                'disposition_status' => 'completed',
                'notes' => 'Kuota 15 siswa untuk periode semester depan.',
            ]
        );

        Letter::firstOrCreate(
            ['letter_number' => '045/TU-OUT/090/IX/2026'],
            [
                'school_id' => $school->id,
                'type' => 'outgoing',
                'category' => 'assignment',
                'sender' => 'Kepala Sekolah',
                'recipient' => 'Guru Pembimbing LKS Kota Bandung',
                'subject' => 'Surat Tugas Pembimbingan Lomba Kompetensi Siswa (LKS) SMK Tingkat Wilayah',
                'letter_date' => now()->subDays(2)->toDateString(),
                'received_or_sent_date' => now()->subDays(1)->toDateString(),
                'status' => 'dispatched',
                'disposition_status' => 'completed',
                'notes' => 'Tembusan kepada pengawas pembina sekolah.',
            ]
        );

        Letter::firstOrCreate(
            ['letter_number' => '046/TU-OUT/005/IX/2026'],
            [
                'school_id' => $school->id,
                'type' => 'outgoing',
                'category' => 'invitation',
                'sender' => 'Manajemen Sekolah & Pengurus Komite',
                'recipient' => 'Orang Tua / Wali Murid Kelas X, XI, XII',
                'subject' => 'Undangan Rapat Pleno Sosialisasi Program Sekolah dan Laporan Tengah Semester',
                'letter_date' => now()->toDateString(),
                'received_or_sent_date' => now()->toDateString(),
                'status' => 'registered',
                'disposition_status' => 'pending',
                'notes' => 'Pelaksanaan hari Sabtu mendatang pukul 08:30 WIB di Ruang Serbaguna.',
            ]
        );

        // 2. PPDB Registrations
        if ($dept1) {
            PpdbRegistration::firstOrCreate(
                ['registration_number' => 'PPDB/2026/0001'],
                [
                    'school_id' => $school->id,
                    'academic_year_id' => $academicYear ? $academicYear->id : 1,
                    'department_id' => $dept1->id,
                    'second_choice_department_id' => $dept2 ? $dept2->id : null,
                    'track' => 'prestasi_akademik',
                    'full_name' => 'Mochamad Rizky Maulana',
                    'nisn' => '0089123451',
                    'nik' => '3273011205080001',
                    'gender' => 'L',
                    'place_of_birth' => 'Bandung',
                    'date_of_birth' => '2008-05-12',
                    'religion' => 'Islam',
                    'phone' => '081234567801',
                    'email' => 'rizky.maulana@example.com',
                    'address' => 'Jl. Dipati Ukur No. 45, Coblong, Kota Bandung',
                    'parent_name' => 'Bambang Sudarsono',
                    'parent_phone' => '081234567800',
                    'parent_job' => 'Wiraswasta',
                    'parent_income' => 'Rp 5.000.000 - Rp 10.000.000',
                    'previous_school' => 'SMP Negeri 1 Bandung',
                    'average_report_score' => 88.50,
                    'status' => 'accepted',
                    'verified_by' => $headmaster ? $headmaster->id : null,
                    'verified_at' => now()->subDays(3),
                    'notes' => 'Nilai akademik sangat tinggi, berkas ijazah & piagam juara olimpiade sains terverifikasi lengkap.',
                ]
            );
        }

        if ($dept2) {
            PpdbRegistration::firstOrCreate(
                ['registration_number' => 'PPDB/2026/0002'],
                [
                    'school_id' => $school->id,
                    'academic_year_id' => $academicYear ? $academicYear->id : 1,
                    'department_id' => $dept2->id,
                    'second_choice_department_id' => $dept1 ? $dept1->id : null,
                    'track' => 'zonasi',
                    'full_name' => 'Siti Aisyah Nurhaliza',
                    'nisn' => '0089123452',
                    'nik' => '3273015509080002',
                    'gender' => 'P',
                    'place_of_birth' => 'Cimahi',
                    'date_of_birth' => '2008-09-15',
                    'religion' => 'Islam',
                    'phone' => '081234567802',
                    'email' => 'aisyah.nur@example.com',
                    'address' => 'Jl. Soekarno Hatta No. 620, Buahbatu, Kota Bandung',
                    'parent_name' => 'Ahmad Hidayat',
                    'parent_phone' => '081234567803',
                    'parent_job' => 'PNS / Guru',
                    'parent_income' => 'Rp 5.000.000 - Rp 10.000.000',
                    'previous_school' => 'SMP Negeri 3 Cimahi',
                    'average_report_score' => 84.00,
                    'status' => 'verified',
                    'verified_by' => $headmaster ? $headmaster->id : null,
                    'verified_at' => now()->subDays(1),
                    'notes' => 'Radius domisili 1.2 KM memenuhi kuota zonasi.',
                ]
            );
        }

        if ($dept3) {
            PpdbRegistration::firstOrCreate(
                ['registration_number' => 'PPDB/2026/0003'],
                [
                    'school_id' => $school->id,
                    'academic_year_id' => $academicYear ? $academicYear->id : 1,
                    'department_id' => $dept3->id,
                    'track' => 'afirmasi',
                    'full_name' => 'Dimas Arya Pratama',
                    'nisn' => '0089123453',
                    'nik' => '3273012011080003',
                    'gender' => 'L',
                    'place_of_birth' => 'Bandung',
                    'date_of_birth' => '2008-11-20',
                    'religion' => 'Islam',
                    'phone' => '081234567804',
                    'email' => 'dimas.arya@example.com',
                    'address' => 'Jl. Kiaracondong No. 110, Batununggal, Kota Bandung',
                    'parent_name' => 'Supriyadi',
                    'parent_phone' => '081234567805',
                    'parent_job' => 'Buruh Harian Lepas',
                    'parent_income' => '< Rp 2.000.000',
                    'previous_school' => 'SMP Negeri 5 Bandung',
                    'average_report_score' => 81.20,
                    'status' => 'submitted',
                    'notes' => 'Menunggu verifikasi kartu KIP / SKTM dinas sosial.',
                ]
            );
        }

        // 3. Guest Book
        GuestBook::firstOrCreate(
            ['badge_number' => 'TAMU-01'],
            [
                'school_id' => $school->id,
                'visitor_name' => 'Dr. H. Suwandi, M.Pd.',
                'institution' => 'Dinas Pendidikan Provinsi Jawa Barat',
                'phone' => '081122334455',
                'identity_type' => 'KTP',
                'identity_number' => '3273010101700001',
                'purpose' => 'Monitoring dan Evaluasi Kesiapan Sarpras Pembelajaran & Akreditasi',
                'target_person' => 'Kepala Sekolah & Tim Manajemen',
                'check_in_at' => now()->subHours(2),
                'status' => 'checked_in',
                'notes' => 'Didampingi oleh 1 orang staf pengawas binaan.',
            ]
        );

        GuestBook::firstOrCreate(
            ['badge_number' => 'TAMU-02'],
            [
                'school_id' => $school->id,
                'visitor_name' => 'Ibu Kartika Dewi, S.E.',
                'institution' => 'Orang Tua Siswa (Kelas XII PPLG 1)',
                'phone' => '081399887766',
                'identity_type' => 'KTP',
                'identity_number' => '3273014506780002',
                'purpose' => 'Konsultasi Peminatan Kuliah & Beasiswa Perguruan Tinggi',
                'target_person' => 'Koordinator Guru BK',
                'check_in_at' => now()->subHours(4),
                'check_out_at' => now()->subHours(3),
                'status' => 'checked_out',
                'notes' => 'Selesai konsultasi, tindak lanjut pengisian formulir SNBP.',
            ]
        );

        // 4. OSIS Election & Candidates
        $election = OsisElection::firstOrCreate(
            ['period' => '2026/2027'],
            [
                'school_id' => $school->id,
                'academic_year_id' => $academicYear ? $academicYear->id : 1,
                'title' => 'Pemilihan Raya Ketua & Wakil Ketua OSIS Periode 2026/2027',
                'start_date' => now()->subDays(1),
                'end_date' => now()->addDays(5),
                'status' => 'active',
            ]
        );

        // Ensure we have at least 4 students for candidate pairs
        while ($students->count() < 4) {
            $idx = $students->count() + 1;
            $newStd = Student::create([
                'school_id' => $school->id,
                'school_level_id' => 1,
                'department_id' => $dept1 ? $dept1->id : 1,
                'nisn' => sprintf('00876543%02d', $idx),
                'nis' => sprintf('262710%02d', $idx),
                'nik' => sprintf('32101215080800%02d', $idx),
                'full_name' => 'Kandidat Siswa '.$idx,
                'gender' => ($idx % 2 === 0) ? 'P' : 'L',
                'place_of_birth' => 'Bandung',
                'date_of_birth' => '2008-01-10',
                'religion' => 'Islam',
                'status' => 'active',
            ]);
            $students->push($newStd);
        }

        if ($students->count() >= 2) {
            $s0 = $students->get(0);
            $s1 = $students->get(1);
            $s2 = $students->get(2);
            $s3 = $students->get(3);

            $paslon1 = OsisCandidate::firstOrCreate(
                ['election_id' => $election->id, 'candidate_number' => 1],
                [
                    'chairman_student_id' => $s0 ? $s0->id : 1,
                    'vice_chairman_student_id' => $s1 ? $s1->id : 1,
                    'vision' => 'Mewujudkan OSIS yang Proaktif, Inovatif, Berkarakter Pancasila, dan Unggul dalam Kolaborasi Digital.',
                    'mission' => "1. Meningkatkan literasi teknologi dan kreativitas siswa melalui workshop industri.\n2. Mengoptimalkan wadah aspirasi siswa berbasis platform digital terbuka.\n3. Menyelenggarakan kegiatan keagamaan, sosial, dan perlombaan seni-olahraga berkala.",
                    'photo_url' => '/images/osis/paslon-1.jpg',
                    'vote_count' => 0,
                ]
            );

            $paslon2 = OsisCandidate::firstOrCreate(
                ['election_id' => $election->id, 'candidate_number' => 2],
                [
                    'chairman_student_id' => $s2 ? $s2->id : 1,
                    'vice_chairman_student_id' => $s3 ? $s3->id : 1,
                    'vision' => 'Membangun Ekosistem Kesiswaan yang Inklusif, Disiplin, Peduli Lingkungan, dan Berdaya Saing Global.',
                    'mission' => "1. Menumbuhkan budaya green-campus dan pengelolaan sampah mandiri di lingkungan sekolah.\n2. Mengembangkan program mentoring sebaya untuk prestasi akademik dan non-akademik.\n3. Mempererat persaudaraan antarkelas melalui festival kreasi siswa dan pekan olahraga sekolah.",
                    'photo_url' => '/images/osis/paslon-2.jpg',
                    'vote_count' => 0,
                ]
            );

            // Simulasi suara awal dari siswa ke-5 dst jika ada
            if ($students->count() > 4) {
                foreach ($students->slice(4) as $idx => $voter) {
                    $pickedCandidate = ($idx % 2 === 0) ? $paslon1 : $paslon2;
                    $hasVoted = OsisVote::where('election_id', $election->id)
                        ->where('voter_student_id', $voter->id)
                        ->exists();

                    if (! $hasVoted) {
                        OsisVote::create([
                            'election_id' => $election->id,
                            'candidate_id' => $pickedCandidate->id,
                            'voter_student_id' => $voter->id,
                            'token_hash' => hash('sha256', "token-voter-{$voter->id}"),
                            'ip_address' => '127.0.0.1',
                            'voted_at' => now()->subMinutes(rand(10, 180)),
                        ]);
                        $pickedCandidate->increment('vote_count');
                    }
                }
            }
        }

        // 5. Extracurriculars
        $ekskuls = [
            [
                'name' => 'Gerakan Pramuka Gudep 06.01-06.02',
                'category' => 'wajib',
                'advisor_staff_id' => $teachers->first()?->id,
                'schedule_day' => 'Jumat, 15:30 - 17:00',
                'location' => 'Lapangan Utama Sekolah',
                'description' => 'Pendidikan kepanduan, pembentukan karakter kemandirian, kepemimpinan, dan kecintaan tanah air.',
            ],
            [
                'name' => 'Paskibra Satuan Rajawali',
                'category' => 'pilihan',
                'advisor_staff_id' => $teachers->get(1)?->id,
                'schedule_day' => 'Sabtu, 08:00 - 11:00',
                'location' => 'Lapangan Upacara Bendera',
                'description' => 'Pelatihan baris-berbaris formal, formasi pengibaran bendera, ketahanan fisik, dan disiplin tinggi.',
            ],
            [
                'name' => 'Palang Merah Remaja (PMR Wira)',
                'category' => 'pilihan',
                'advisor_staff_id' => $teachers->first()?->id,
                'schedule_day' => 'Rabu, 15:30 - 17:00',
                'location' => 'Ruang UKS & Aula',
                'description' => 'Pelatihan pertolongan pertama pada kecelakaan (P3K), kesiapsiagaan bencana, dan donor darah.',
            ],
            [
                'name' => 'IT & Cyber Robotics Club',
                'category' => 'pilihan',
                'advisor_staff_id' => $teachers->get(1)?->id,
                'schedule_day' => 'Kamis, 15:30 - 17:30',
                'location' => 'Laboratorium Komputer Rekayasa Perangkat Lunak',
                'description' => 'Eksplorasi pemrograman web & mobile, competitive programming, IoT, dan perakitan robotika.',
            ],
            [
                'name' => 'Klub Futsal & Sepakbola',
                'category' => 'pilihan',
                'advisor_staff_id' => $teachers->first()?->id,
                'schedule_day' => 'Selasa, 16:00 - 17:30',
                'location' => 'Lapangan Olahraga Tertutup (Indoor)',
                'description' => 'Pengembangan bakat olahraga futsal, taktik permainan, pembinaan fisik, dan kompetisi antarpelajar.',
            ],
        ];

        foreach ($ekskuls as $item) {
            Extracurricular::firstOrCreate(
                ['school_id' => $school->id, 'name' => $item['name']],
                array_merge($item, ['school_id' => $school->id, 'is_active' => true])
            );
        }
    }
}
