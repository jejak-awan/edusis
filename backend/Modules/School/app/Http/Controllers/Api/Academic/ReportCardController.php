<?php

declare(strict_types=1);

namespace Modules\School\Http\Controllers\Api\Academic;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\School\Http\Controllers\Api\Common\BaseController;
use Modules\School\Models\Academic\AcademicYear;
use Modules\School\Models\Academic\Assessment;
use Modules\School\Models\Academic\Attendance;
use Modules\School\Models\Academic\Grade;
use Modules\School\Models\Academic\ReportCard;
use Modules\School\Models\Academic\StudyGroup;
use Modules\School\Models\Academic\Subject;
use Modules\School\Models\Institution\School;
use Modules\School\Models\Student\Student;

class ReportCardController extends BaseController
{
    private function resolveSchoolId(Request $request): int
    {
        $schoolId = $request->header('X-School-Id');
        if ($schoolId && is_numeric($schoolId)) {
            return (int) $schoolId;
        }

        $school = School::first();
        return $school ? $school->id : 1;
    }

    public function ledger(Request $request): JsonResponse
    {
        $schoolId = $this->resolveSchoolId($request);
        $studyGroupId = (int) $request->input('study_group_id');

        if (!$studyGroupId) {
            return $this->sendError('study_group_id is required', [], 422);
        }

        $studyGroup = StudyGroup::with(['students', 'academicYear', 'department'])
            ->where('school_id', $schoolId)
            ->findOrFail($studyGroupId);

        $activeYear = AcademicYear::where('school_id', $schoolId)
            ->where('is_active', true)
            ->with('activeSemester')
            ->first();

        $semesterId = $activeYear && $activeYear->activeSemester ? $activeYear->activeSemester->id : 1;

        $subjects = Subject::where('school_id', $schoolId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'code', 'name']);

        $reportCards = ReportCard::where('study_group_id', $studyGroupId)
            ->where('semester_id', $semesterId)
            ->get()
            ->groupBy('student_id');

        $studentsData = $studyGroup->students->map(function ($student) use ($subjects, $reportCards, $studyGroupId) {
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

            // Attendance stats
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

        return $this->sendResponse([
            'study_group' => $studyGroup,
            'subjects' => $subjects,
            'students' => $studentsData,
        ], 'Ledger data retrieved successfully');
    }

    public function calculate(Request $request): JsonResponse
    {
        $schoolId = $this->resolveSchoolId($request);
        $studyGroupId = (int) $request->input('study_group_id');

        if (!$studyGroupId) {
            return $this->sendError('study_group_id is required', [], 422);
        }

        $activeYear = AcademicYear::where('school_id', $schoolId)
            ->where('is_active', true)
            ->with('activeSemester')
            ->first();

        $academicYearId = $activeYear ? $activeYear->id : 1;
        $semesterId = $activeYear && $activeYear->activeSemester ? $activeYear->activeSemester->id : 1;

        $assessments = Assessment::where('study_group_id', $studyGroupId)
            ->where('semester_id', $semesterId)
            ->with(['grades', 'subject'])
            ->get();

        if ($assessments->isEmpty()) {
            return $this->sendError('No assessments found for this study group in the active semester', [], 400);
        }

        // Group by subject and calculate per student
        $assessmentsBySubject = $assessments->groupBy('subject_id');
        $calculatedCount = 0;

        foreach ($assessmentsBySubject as $subjectId => $subjectAssessments) {
            $studentGrades = [];

            foreach ($subjectAssessments as $asm) {
                $weight = $asm->weight ?: 1;
                foreach ($asm->grades as $grade) {
                    $effectiveScore = (float) ($grade->remedial_score ?? $grade->score);
                    if (!isset($studentGrades[$grade->student_id])) {
                        $studentGrades[$grade->student_id] = [
                            'weighted_sum' => 0,
                            'total_weight' => 0,
                            'highest_score' => 0,
                            'lowest_score' => 100,
                            'highest_topic' => $asm->title,
                            'lowest_topic' => $asm->title,
                        ];
                    }
                    $studentGrades[$grade->student_id]['weighted_sum'] += ($effectiveScore * $weight);
                    $studentGrades[$grade->student_id]['total_weight'] += $weight;

                    if ($effectiveScore >= $studentGrades[$grade->student_id]['highest_score']) {
                        $studentGrades[$grade->student_id]['highest_score'] = $effectiveScore;
                        $studentGrades[$grade->student_id]['highest_topic'] = $asm->title;
                    }
                    if ($effectiveScore <= $studentGrades[$grade->student_id]['lowest_score']) {
                        $studentGrades[$grade->student_id]['lowest_score'] = $effectiveScore;
                        $studentGrades[$grade->student_id]['lowest_topic'] = $asm->title;
                    }
                }
            }

            foreach ($studentGrades as $studentId => $stats) {
                $finalScore = $stats['total_weight'] > 0
                    ? round($stats['weighted_sum'] / $stats['total_weight'], 2)
                    : 0;

                $letterGrade = 'D';
                if ($finalScore >= 85) $letterGrade = 'A';
                elseif ($finalScore >= 75) $letterGrade = 'B';
                elseif ($finalScore >= 60) $letterGrade = 'C';

                $achievedDesc = "Menunjukkan penguasaan yang sangat baik dalam materi {$stats['highest_topic']}.";
                $improvedDesc = $stats['lowest_score'] < 75
                    ? "Perlu bimbingan dan peningkatan pemahaman lebih lanjut dalam materi {$stats['lowest_topic']}."
                    : "Mampu mempertahankan capaian optimal pada seluruh materi.";

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

        return $this->sendResponse([
            'calculated_records' => $calculatedCount,
        ], 'Report cards calculated successfully');
    }

    public function studentReport(Request $request, int $studentId): JsonResponse
    {
        $schoolId = $this->resolveSchoolId($request);
        $student = Student::with(['department', 'studyGroups'])->findOrFail($studentId);

        $activeYear = AcademicYear::where('school_id', $schoolId)
            ->where('is_active', true)
            ->with('activeSemester')
            ->first();

        $semesterId = $activeYear && $activeYear->activeSemester ? $activeYear->activeSemester->id : 1;

        $reportCards = ReportCard::where('student_id', $studentId)
            ->where('semester_id', $semesterId)
            ->with(['subject', 'studyGroup.homeroomTeacher'])
            ->get();

        $studyGroup = $student->studyGroups->first();

        $attendance = [
            'sick' => Attendance::where('student_id', $studentId)->where('status', 'S')->count(),
            'permission' => Attendance::where('student_id', $studentId)->where('status', 'I')->count(),
            'absent' => Attendance::where('student_id', $studentId)->where('status', 'A')->count(),
        ];

        return $this->sendResponse([
            'student' => $student,
            'academic_year' => $activeYear,
            'study_group' => $studyGroup,
            'report_cards' => $reportCards,
            'attendance' => $attendance,
            'homeroom_teacher' => $studyGroup && $studyGroup->homeroomTeacher ? $studyGroup->homeroomTeacher->full_name : 'Wali Kelas',
        ], 'Student report card retrieved successfully');
    }
}
