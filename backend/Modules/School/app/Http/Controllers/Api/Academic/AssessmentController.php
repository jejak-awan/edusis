<?php

declare(strict_types=1);

namespace Modules\School\Http\Controllers\Api\Academic;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\School\Http\Controllers\Api\Common\BaseController;
use Modules\School\Http\Requests\Academic\StoreAssessmentRequest;
use Modules\School\Http\Requests\Academic\StoreBulkGradesRequest;
use Modules\School\Models\Academic\AcademicYear;
use Modules\School\Models\Academic\Assessment;
use Modules\School\Models\Academic\Grade;

class AssessmentController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();
        $query = Assessment::where('school_id', $schoolId)
            ->with(['studyGroup', 'subject', 'teacher', 'learningObjective']);

        if ($request->filled('study_group_id')) {
            $query->where('study_group_id', $request->input('study_group_id'));
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->input('subject_id'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        $perPage = min($request->integer('per_page', 20), 100);
        $assessments = $query->latest('date')->paginate($perPage);

        return $this->sendResponse($assessments, 'Assessments retrieved successfully');
    }

    public function store(StoreAssessmentRequest $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();

        $activeYear = AcademicYear::where('school_id', $schoolId)
            ->where('is_active', true)
            ->with('activeSemester')
            ->first();

        $academicYearId = $activeYear ? $activeYear->id : 1;
        $semesterId = $activeYear && $activeYear->activeSemester ? $activeYear->activeSemester->id : 1;

        $assessment = Assessment::create(array_merge($request->validated(), [
            'school_id' => $schoolId,
            'academic_year_id' => $academicYearId,
            'semester_id' => $semesterId,
        ]));

        return $this->sendResponse($assessment, 'Assessment created successfully', 201);
    }

    public function show(int $id): JsonResponse
    {
        $assessment = Assessment::with([
            'studyGroup',
            'subject',
            'teacher',
            'learningObjective',
            'grades.student',
        ])->findOrFail($id);

        return $this->sendResponse($assessment, 'Assessment details retrieved');
    }

    public function grades(int $id): JsonResponse
    {
        $assessment = Assessment::with('studyGroup.students')->findOrFail($id);
        assert($assessment instanceof Assessment);
        $studyGroup = $assessment->studyGroup;
        /** @var \Illuminate\Database\Eloquent\Collection<int, \Modules\School\Models\Student\Student> $students */
        $students = $studyGroup instanceof \Modules\School\Models\Academic\StudyGroup ? $studyGroup->students : collect();

        $existingGrades = Grade::where('assessment_id', $id)
            ->get()
            ->keyBy('student_id');

        $result = $students->map(function (\Modules\School\Models\Student\Student $student) use ($existingGrades): array {
            /** @var Grade|null $grade */
            $grade = $existingGrades->get($student->id);

            return [
                'student_id' => $student->id,
                'nisn' => $student->nisn,
                'nis' => $student->nis,
                'full_name' => $student->full_name,
                'gender' => $student->gender,
                'score' => $grade ? (float) $grade->score : 0,
                'remedial_score' => $grade && $grade->remedial_score !== null ? (float) $grade->remedial_score : null,
                'notes' => $grade ? $grade->notes : null,
            ];
        });

        return $this->sendResponse($result, 'Assessment grades retrieved');
    }

    public function storeBulkGrades(StoreBulkGradesRequest $request, int $id): JsonResponse
    {
        $assessment = Assessment::findOrFail($id);
        assert($assessment instanceof Assessment);

        $validated = $request->validated();
        /** @var array<int, array<string, mixed>> $grades */
        $grades = isset($validated['grades']) && is_array($validated['grades']) ? $validated['grades'] : [];

        foreach ($grades as $item) {
            $studentId = isset($item['student_id']) && is_numeric($item['student_id']) ? (int) $item['student_id'] : 0;
            $score = isset($item['score']) && is_numeric($item['score']) ? (float) $item['score'] : 0.0;
            $remedialScore = isset($item['remedial_score']) && is_numeric($item['remedial_score']) ? (float) $item['remedial_score'] : null;
            $notes = isset($item['notes']) && is_string($item['notes']) ? $item['notes'] : null;

            Grade::updateOrCreate(
                [
                    'assessment_id' => $assessment->id,
                    'student_id' => $studentId,
                ],
                [
                    'score' => $score,
                    'remedial_score' => $remedialScore,
                    'notes' => $notes,
                ]
            );
        }

        return $this->sendResponse(null, 'Grades saved successfully');
    }

    public function destroy(int $id): JsonResponse
    {
        $assessment = Assessment::findOrFail($id);
        $assessment->delete();

        return $this->sendResponse(null, 'Assessment deleted successfully');
    }
}
