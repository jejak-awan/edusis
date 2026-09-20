<?php

declare(strict_types=1);

namespace Modules\School\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\School\Models\Academic\AcademicYear;
use Modules\School\Models\Academic\Assessment;
use Modules\School\Models\Academic\Grade;
use Modules\School\Models\Academic\LearningObjective;
use Modules\School\Models\Academic\P5Assessment;
use Modules\School\Models\Academic\P5Project;
use Modules\School\Models\Academic\ReportCard;
use Modules\School\Models\Academic\StudyGroup;
use Modules\School\Models\Academic\Subject;
use Modules\School\Models\Counseling\CounselingCase;
use Modules\School\Models\HR\Staff;
use Modules\School\Models\Institution\School;
use Modules\School\Models\Student\Student;

class SchoolPhase3Seeder extends Seeder
{
    public function run(): void
    {
        $school = School::first();
        if (!$school) return;

        $academicYear = AcademicYear::where('school_id', $school->id)->where('is_active', true)->first();
        $semester = $academicYear ? $academicYear->activeSemester : null;
        $studyGroup = StudyGroup::where('school_id', $school->id)->first();
        $subject = Subject::where('school_id', $school->id)->first();
        $teacher = Staff::where('school_id', $school->id)->first();
        $students = Student::where('school_id', $school->id)->get();

        if (!$academicYear || !$semester || !$studyGroup || !$subject || !$teacher || $students->isEmpty()) {
            return;
        }

        // 1. Learning Objectives (Tujuan Pembelajaran)
        $tp1 = LearningObjective::updateOrCreate(
            ['school_id' => $school->id, 'subject_id' => $subject->id, 'code' => 'TP-01'],
            [
                'description' => 'Menerapkan prinsip arsitektur modular dan Clean Code dalam perancangan sistem informasi',
                'phase' => 'E',
                'order_index' => 1,
                'is_active' => true,
            ]
        );

        $tp2 = LearningObjective::updateOrCreate(
            ['school_id' => $school->id, 'subject_id' => $subject->id, 'code' => 'TP-02'],
            [
                'description' => 'Membangun antarmuka pengguna responsif dan REST API terstandar dengan validasi ketat',
                'phase' => 'E',
                'order_index' => 2,
                'is_active' => true,
            ]
        );

        // 2. Assessments (Formatif, Sumatif Lingkup Materi, Sumatif Akhir)
        $asm1 = Assessment::updateOrCreate(
            [
                'study_group_id' => $studyGroup->id,
                'subject_id' => $subject->id,
                'title' => 'Formatif 1: Perancangan Arsitektur Web',
            ],
            [
                'school_id' => $school->id,
                'academic_year_id' => $academicYear->id,
                'semester_id' => $semester->id,
                'teacher_id' => $teacher->id,
                'learning_objective_id' => $tp1->id,
                'type' => 'formative',
                'weight' => 1,
                'max_score' => 100,
                'date' => now()->subDays(10)->toDateString(),
                'description' => 'Tugas mandiri perancangan skema relasi dan modul',
            ]
        );

        $asm2 = Assessment::updateOrCreate(
            [
                'study_group_id' => $studyGroup->id,
                'subject_id' => $subject->id,
                'title' => 'Sumatif Lingkup Materi 1: Implementasi API',
            ],
            [
                'school_id' => $school->id,
                'academic_year_id' => $academicYear->id,
                'semester_id' => $semester->id,
                'teacher_id' => $teacher->id,
                'learning_objective_id' => $tp2->id,
                'type' => 'summative_material',
                'weight' => 2,
                'max_score' => 100,
                'date' => now()->subDays(5)->toDateString(),
                'description' => 'Ujian praktik pembuatan endpoint CRUD dan penanganan error',
            ]
        );

        $asm3 = Assessment::updateOrCreate(
            [
                'study_group_id' => $studyGroup->id,
                'subject_id' => $subject->id,
                'title' => 'Sumatif Akhir Semester (SAS)',
            ],
            [
                'school_id' => $school->id,
                'academic_year_id' => $academicYear->id,
                'semester_id' => $semester->id,
                'teacher_id' => $teacher->id,
                'type' => 'summative_final',
                'weight' => 3,
                'max_score' => 100,
                'date' => now()->toDateString(),
                'description' => 'Evaluasi komprehensif semester ganjil',
            ]
        );

        // 3. Grades per student
        $scoreMatrix = [
            0 => ['f1' => 88, 's1' => 90, 'sas' => 92, 'remedial' => null],
            1 => ['f1' => 95, 's1' => 98, 'sas' => 96, 'remedial' => null],
            2 => ['f1' => 70, 's1' => 78, 'sas' => 80, 'remedial' => 78],
        ];

        foreach ($students as $index => $student) {
            $scores = $scoreMatrix[$index % count($scoreMatrix)];

            Grade::updateOrCreate(
                ['assessment_id' => $asm1->id, 'student_id' => $student->id],
                ['score' => $scores['f1'], 'notes' => 'Tuntas dengan baik']
            );

            Grade::updateOrCreate(
                ['assessment_id' => $asm2->id, 'student_id' => $student->id],
                ['score' => $scores['s1'], 'remedial_score' => $scores['remedial'], 'notes' => 'Praktik tuntas']
            );

            Grade::updateOrCreate(
                ['assessment_id' => $asm3->id, 'student_id' => $student->id],
                ['score' => $scores['sas'], 'notes' => 'Sangat memuaskan']
            );

            // 4. Report Card Record Kurikulum Merdeka
            $finalScore = round(($scores['f1'] * 1 + ($scores['remedial'] ?? $scores['s1']) * 2 + $scores['sas'] * 3) / 6, 2);
            $gradeLetter = $finalScore >= 85 ? 'A' : ($finalScore >= 75 ? 'B' : 'C');

            ReportCard::updateOrCreate(
                [
                    'school_id' => $school->id,
                    'academic_year_id' => $academicYear->id,
                    'semester_id' => $semester->id,
                    'study_group_id' => $studyGroup->id,
                    'student_id' => $student->id,
                    'subject_id' => $subject->id,
                ],
                [
                    'final_score' => $finalScore,
                    'letter_grade' => $gradeLetter,
                    'achieved_competency' => "Menunjukkan penguasaan istimewa dalam mengimplementasikan REST API terstandar.",
                    'improved_competency' => "Perlu mempertahankan ketelitian dalam dokumentasi kode program.",
                ]
            );
        }

        // 5. Proyek P5
        $p5 = P5Project::updateOrCreate(
            [
                'school_id' => $school->id,
                'academic_year_id' => $academicYear->id,
                'title' => 'Sistem Cerdas Pengelolaan Sampah Organik Sekolah Berkelanjutan',
            ],
            [
                'coordinator_id' => $teacher->id,
                'theme' => 'Gaya Hidup Berkelanjutan & Rekayasa Teknologi',
                'phase' => 'E',
                'description' => 'Proyek lintas disiplin ilmu memanfaatkan sensor IoT dan pembuatan eco-enzyme untuk pupuk taman sekolah.',
            ]
        );

        if ($students->isNotEmpty()) {
            P5Assessment::updateOrCreate(
                [
                    'project_id' => $p5->id,
                    'student_id' => $students[0]->id,
                    'dimension' => 'Bergotong Royong',
                ],
                [
                    'predicate' => 'SAB',
                    'notes' => 'Aktif mengkoordinasikan kelompok dalam perakitan sensor',
                ]
            );

            P5Assessment::updateOrCreate(
                [
                    'project_id' => $p5->id,
                    'student_id' => $students[0]->id,
                    'dimension' => 'Bernalar Kritis',
                ],
                [
                    'predicate' => 'BSH',
                    'notes' => 'Mampu menganalisis data kelembaban sampah secara logis',
                ]
            );
        }

        // 6. Counseling Case (BK)
        if ($students->isNotEmpty()) {
            CounselingCase::updateOrCreate(
                [
                    'school_id' => $school->id,
                    'case_number' => 'BK/2026/09/001',
                ],
                [
                    'student_id' => $students->last()->id,
                    'counselor_id' => $teacher->id,
                    'academic_year_id' => $academicYear->id,
                    'semester_id' => $semester->id,
                    'date' => now()->subDays(2)->toDateString(),
                    'category' => 'belajar',
                    'confidentiality' => 'internal',
                    'problem_description' => 'Siswa merasa kesulitan membagi waktu antara jadwal latihan ekstrakurikuler dan tugas praktikum pemrograman web.',
                    'counseling_steps' => 'Sesi konseling individual, eksplorasi hambatan, dan teknik penyusunan time management matrix Eisenhower.',
                    'solution_plan' => 'Siswa membuat jadwal belajar mingguan mandiri dan dipantau oleh wali kelas & guru BK.',
                    'follow_up_notes' => 'Progres pekan pertama menunjukkan perbaikan kehadiran tugas tepat waktu.',
                    'status' => 'in_progress',
                ]
            );
        }
    }
}
