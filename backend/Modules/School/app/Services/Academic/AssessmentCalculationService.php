<?php

declare(strict_types=1);

namespace Modules\School\Services\Academic;

use Illuminate\Support\Facades\DB;
use Modules\School\Exceptions\SchoolModuleException;
use Modules\School\Models\Academic\AcademicYear;
use Modules\School\Models\Academic\Assessment;
use Modules\School\Models\Academic\Attendance;
use Modules\School\Models\Academic\Grade;
use Modules\School\Models\Academic\ReportCard;
use Modules\School\Models\Academic\StudyGroup;
use Modules\School\Models\Academic\Subject;
use Modules\School\Models\Student\Student;

class AssessmentCalculationService
{
    /**
     * Hitung agregat nilai raport Kurikulum Merdeka per rombel untuk semester aktif.
     */
    public function calculateReportCards(int $studyGroupId, int $schoolId): int
    {
        $activeYear = AcademicYear::where('school_id', $schoolId)
            ->where('is_active', true)
            ->with('activeSemester')
            ->first();

        $academicYearId = $activeYear ? $activeYear->id : 1;
        $semesterId = $activeYear?->activeSemester->id ?? 1;

        $assessments = Assessment::where('study_group_id', $studyGroupId)
            ->where('semester_id', $semesterId)
            ->with(['grades', 'subject'])
            ->get();

        if ($assessments->isEmpty()) {
            throw new SchoolModuleException('Tidak ada asesmen yang ditemukan untuk rombel ini pada semester aktif.', 400);
        }

        return DB::transaction(function () use ($assessments, $schoolId, $academicYearId, $semesterId, $studyGroupId) {
            $assessmentsBySubject = $assessments->groupBy('subject_id');
            $calculatedCount = 0;

            foreach ($assessmentsBySubject as $subjectId => $subjectAssessments) {
                $studentGrades = [];

                foreach ($subjectAssessments as $asm) {
                    if (! ($asm instanceof Assessment)) {
                        continue;
                    }
                    $weight = $asm->weight ?: 1;
                    foreach ($asm->grades as $grade) {
                        if (! ($grade instanceof Grade)) {
                            continue;
                        }
                        $studentId = $grade->student_id;
                        $effectiveScore = (float) ($grade->remedial_score ?? $grade->score);
                        if (! isset($studentGrades[$studentId])) {
                            $studentGrades[$studentId] = [
                                'weighted_sum' => 0,
                                'total_weight' => 0,
                                'highest_score' => 0,
                                'lowest_score' => 100,
                                'highest_topic' => $asm->title,
                                'lowest_topic' => $asm->title,
                            ];
                        }
                        $studentGrades[$studentId]['weighted_sum'] += ($effectiveScore * $weight);
                        $studentGrades[$studentId]['total_weight'] += $weight;

                        if ($effectiveScore >= $studentGrades[$studentId]['highest_score']) {
                            $studentGrades[$studentId]['highest_score'] = $effectiveScore;
                            $studentGrades[$studentId]['highest_topic'] = $asm->title;
                        }
                        if ($effectiveScore <= $studentGrades[$studentId]['lowest_score']) {
                            $studentGrades[$studentId]['lowest_score'] = $effectiveScore;
                            $studentGrades[$studentId]['lowest_topic'] = $asm->title;
                        }
                    }
                }

                foreach ($studentGrades as $studentId => $stats) {
                    $finalScore = $stats['total_weight'] > 0
                        ? round($stats['weighted_sum'] / $stats['total_weight'], 2)
                        : 0;

                    $letterGrade = 'D';
                    if ($finalScore >= 85) {
                        $letterGrade = 'A';
                    } elseif ($finalScore >= 75) {
                        $letterGrade = 'B';
                    } elseif ($finalScore >= 60) {
                        $letterGrade = 'C';
                    }

                    $achievedDesc = "Menunjukkan penguasaan yang sangat baik dalam materi {$stats['highest_topic']}.";
                    $improvedDesc = $stats['lowest_score'] < 75
                        ? "Perlu bimbingan dan peningkatan pemahaman lebih lanjut dalam materi {$stats['lowest_topic']}."
                        : 'Mampu mempertahankan capaian optimal pada seluruh materi.';

                    ReportCard::updateOrCreate(
                        [
                            'school_id' => $schoolId,
                            'academic_year_id' => $academicYearId,
                            'semester_id' => $semesterId,
                            'study_group_id' => $studyGroupId,
                            'student_id' => $studentId,
                            'subject_id' => $subjectId,
                        ],
                        [
                            'final_score' => $finalScore,
                            'letter_grade' => $letterGrade,
                            'achieved_competency' => $achievedDesc,
                            'improved_competency' => $improvedDesc,
                        ]
                    );
                    $calculatedCount++;
                }
            }

            return $calculatedCount;
        });
    }

    /**
     * Susun leger nilai per rombel termasuk absensi dan rerata capaian.
     *
     * @return array<string, mixed>
     */
    public function getLedger(int $studyGroupId, int $schoolId): array
    {
        $studyGroup = StudyGroup::with(['students', 'academicYear', 'department'])
            ->where('school_id', $schoolId)
            ->findOrFail($studyGroupId);

        $activeYear = AcademicYear::where('school_id', $schoolId)
            ->where('is_active', true)
            ->with('activeSemester')
            ->first();

        $semesterId = $activeYear?->activeSemester?->id ?? 1;

        $subjects = Subject::where('school_id', $schoolId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'code', 'name']);

        $reportCards = ReportCard::where('study_group_id', $studyGroupId)
            ->where('semester_id', $semesterId)
            ->get()
            ->groupBy('student_id');

        $studentsData = $studyGroup->students->map(function ($student) use ($subjects, $reportCards, $studyGroupId): array {
            /** @var Student $student */
            $cards = $reportCards->get($student->id, collect())->keyBy('subject_id');

            $subjectScores = [];
            $totalScore = 0;
            $count = 0;

            foreach ($subjects as $subject) {
                $card = $cards->get($subject->id);
                $score = $card ? (float) $card->final_score : null;
                $subjectScores[$subject->id] = [
                    'score' => $score,
                    'letter_grade' => $card ? $card->letter_grade : '-',
                ];
                if ($score !== null) {
                    $totalScore += $score;
                    $count++;
                }
            }

            $attRecords = Attendance::where('student_id', $student->id)
                ->where('study_group_id', $studyGroupId)
                ->get();

            $sick = $attRecords->where('status', 'S')->count();
            $permit = $attRecords->where('status', 'I')->count();
            $absent = $attRecords->where('status', 'A')->count();

            return [
                'student_id' => $student->id,
                'nisn' => $student->nisn,
                'nis' => $student->nis,
                'full_name' => $student->full_name,
                'gender' => $student->gender,
                'subject_scores' => $subjectScores,
                'average_score' => $count > 0 ? round($totalScore / $count, 2) : 0,
                'attendance' => [
                    'sick' => $sick,
                    'permission' => $permit,
                    'absent' => $absent,
                ],
            ];
        });

        return [
            'study_group' => $studyGroup,
            'subjects' => $subjects,
            'students' => $studentsData,
        ];
    }

    /**
     * Dapatkan transkrip raport individu siswa untuk semester berjalan.
     *
     * @return array<string, mixed>
     */
    public function getStudentReport(int $studentId, int $schoolId): array
    {
        $student = Student::with(['department', 'studyGroups'])->findOrFail($studentId);

        $activeYear = AcademicYear::where('school_id', $schoolId)
            ->where('is_active', true)
            ->with('activeSemester')
            ->first();

        $academicYearId = $activeYear?->id ?? 1;
        $semesterId = $activeYear?->activeSemester->id ?? 1;

        $reportCards = ReportCard::where('student_id', $studentId)
            ->where('academic_year_id', $academicYearId)
            ->where('semester_id', $semesterId)
            ->with('subject')
            ->get();

        $studyGroup = $student->studyGroups->first();
        $studyGroupId = $studyGroup ? $studyGroup->id : null;

        $attRecords = Attendance::where('student_id', $studentId);
        if ($studyGroupId) {
            $attRecords->where('study_group_id', $studyGroupId);
        }
        $attList = $attRecords->get();

        $attSummary = [
            'sick' => $attList->where('status', 'S')->count(),
            'permission' => $attList->where('status', 'I')->count(),
            'absent' => $attList->where('status', 'A')->count(),
        ];

        return [
            'student' => $student,
            'academic_year' => $activeYear,
            'semester' => $activeYear?->activeSemester,
            'study_group' => $studyGroup,
            'report_cards' => $reportCards,
            'attendance' => $attSummary,
            'attendance_summary' => $attSummary,
        ];
    }
}
