<?php

declare(strict_types=1);

namespace Modules\School\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\School\Models\Academic\AcademicYear;
use Modules\School\Models\HR\Staff;
use Modules\School\Models\Institution\School;
use Modules\School\Models\Student\Student;
use Modules\School\Models\Voting\VoteBallot;
use Modules\School\Models\Voting\VoteCandidate;
use Modules\School\Models\Voting\VoteEvent;
use Modules\School\Models\Voting\VoteVoter;

class SchoolVotingEngineSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::first();
        if (!$school) return;

        $academicYear = AcademicYear::where('school_id', $school->id)->where('is_active', true)->first();
        $students = Student::where('school_id', $school->id)->get();
        $staff = Staff::where('school_id', $school->id)->get();

        // -------------------------------------------------------------
        // EVENT 1: OSIS ELECTION (Mode: Hybrid / Bilik Suara Kiosk & Online)
        // -------------------------------------------------------------
        $osisEvent = VoteEvent::firstOrCreate(
            [
                'school_id' => $school->id,
                'title' => 'Pemilihan Raya Ketua & Wakil Ketua OSIS 2026/2027',
            ],
            [
                'academic_year_id' => $academicYear ? $academicYear->id : null,
                'type' => 'osis',
                'target_audience' => 'all_students',
                'voting_mode' => 'hybrid',
                'description' => 'Pesta demokrasi tahunan pemilihan Ketua dan Wakil Ketua OSIS dengan asas Luber Jurdil (Langsung, Umum, Bebas, Rahasia, Jujur, dan Adil) berbasis Bilik Suara Kiosk Token & Mobile.',
                'start_date' => now()->subHours(6),
                'end_date' => now()->addDays(2),
                'status' => 'active',
                'allow_abstain' => false,
            ]
        );

        // Paslon 1
        $cand1 = VoteCandidate::firstOrCreate(
            [
                'event_id' => $osisEvent->id,
                'candidate_number' => 1,
            ],
            [
                'title' => 'PASLON 01: Aditya Pratama & Salsabila Putri',
                'leader_student_id' => $students->isNotEmpty() ? $students->first()->id : null,
                'vice_student_id' => $students->count() > 1 ? $students->get(1)->id : null,
                'vision' => 'Mewujudkan OSIS yang Progresif, Inklusif, Kreatif, dan Menjadi Wadah Kolaborasi Talenta Digital Vokasi.',
                'mission' => "1. Menumbuhkan budaya prestasi akademik dan non-akademik di era AI.\n2. Optimalisasi platform digital dan sarana aspirasi siswa 24/7.\n3. Mempererat jejaring industri dan komunitas ekstrakurikuler terpadu.",
                'photo_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=400&auto=format&fit=crop&q=80',
                'vote_count' => 0,
            ]
        );

        // Paslon 2
        $cand2 = VoteCandidate::firstOrCreate(
            [
                'event_id' => $osisEvent->id,
                'candidate_number' => 2,
            ],
            [
                'title' => 'PASLON 02: Fathan Al-Ghifari & Nabila Maharani',
                'leader_student_id' => $students->count() > 2 ? $students->get(2)->id : null,
                'vice_student_id' => $students->count() > 3 ? $students->get(3)->id : null,
                'vision' => 'Transformasi OSIS sebagai Katalisator Karakter Pelajar Pancasila, Mandiri, dan Berjiwa Wirausaha Berkelanjutan.',
                'mission' => "1. Meningkatkan kepedulian lingkungan hidup dan eco-school.\n2. Mengembangkan inkubasi unit produksi kewirausahaan siswa (SPW).\n3. Penguatan solidaritas kebhinekaan antarkelas.",
                'photo_url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&auto=format&fit=crop&q=80',
                'vote_count' => 0,
            ]
        );

        // Generate DPT Voters for OSIS
        foreach ($students as $index => $student) {
            $voter = VoteVoter::firstOrCreate(
                [
                    'event_id' => $osisEvent->id,
                    'student_id' => $student->id,
                ],
                [
                    'voter_type' => 'student',
                    'voter_name' => $student->full_name,
                    'voter_identifier' => $student->nisn ?: ('STD-' . $student->id),
                    'token' => VoteVoter::generateUniqueToken($osisEvent->id),
                    'token_generated_at' => now(),
                    'has_voted' => false,
                ]
            );

            // Simulate the first few voters having already voted
            if ($index < 8 && !$voter->has_voted) {
                $chosenCandidate = ($index % 3 === 0) ? $cand2 : $cand1;
                $votedAt = now()->subMinutes(rand(10, 240));

                $voter->update([
                    'has_voted' => true,
                    'voted_at' => $votedAt,
                ]);

                VoteBallot::create([
                    'event_id' => $osisEvent->id,
                    'candidate_id' => $chosenCandidate->id,
                    'vote_mode' => 'kiosk',
                    'token_hash' => hash('sha256', $voter->token),
                    'ip_address' => '10.20.0.' . rand(10, 99),
                    'device_info' => 'Bilik Suara Kiosk Lab Komputer TPS-1',
                    'voted_at' => $votedAt,
                ]);

                $chosenCandidate->increment('vote_count');
            }
        }

        // -------------------------------------------------------------
        // EVENT 2: POLLING GURU FAVORIT HARI GURU NASIONAL
        // -------------------------------------------------------------
        $guruEvent = VoteEvent::firstOrCreate(
            [
                'school_id' => $school->id,
                'title' => 'Polling Anugerah Guru Favorit & Inspiratif HGN 2026',
            ],
            [
                'academic_year_id' => $academicYear ? $academicYear->id : null,
                'type' => 'guru_favorit',
                'target_audience' => 'all_students',
                'voting_mode' => 'online_login',
                'description' => 'Apresiasi seluruh peserta didik kepada bapak/ibu guru yang paling berdedikasi, ramah, inovatif dalam pembelajaran Kurikulum Merdeka.',
                'start_date' => now()->subDays(1),
                'end_date' => now()->addDays(5),
                'status' => 'active',
                'allow_abstain' => true,
            ]
        );

        if ($staff->isNotEmpty()) {
            foreach ($staff->take(3) as $num => $teacher) {
                VoteCandidate::firstOrCreate(
                    [
                        'event_id' => $guruEvent->id,
                        'candidate_number' => $num + 1,
                    ],
                    [
                        'title' => $teacher->full_name,
                        'staff_id' => $teacher->id,
                        'vision' => 'Mengajar dengan hati, menuntun kodrat anak mencapai kebahagiaan setinggi-tingginya.',
                        'mission' => 'Menciptakan suasana kelas yang menyenangkan, eksploratif, dan tanpa diskriminasi.',
                        'photo_url' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=400&auto=format&fit=crop&q=80',
                        'vote_count' => rand(5, 18),
                    ]
                );
            }
        }

        // -------------------------------------------------------------
        // EVENT 3: MPK PRESIDIUM ELECTION (Upcoming)
        // -------------------------------------------------------------
        VoteEvent::firstOrCreate(
            [
                'school_id' => $school->id,
                'title' => 'Pemilihan Formatur Presidium Majelis Perwakilan Kelas (MPK)',
            ],
            [
                'academic_year_id' => $academicYear ? $academicYear->id : null,
                'type' => 'mpk',
                'target_audience' => 'specific_classes',
                'voting_mode' => 'token_kiosk',
                'description' => 'Musyawarah perwakilan kelas untuk menentukan pimpinan legislatif siswa MPK SMKN 6 Bandung.',
                'start_date' => now()->addDays(3),
                'end_date' => now()->addDays(7),
                'status' => 'active',
                'allow_abstain' => false,
            ]
        );
    }
}
