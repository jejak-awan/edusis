<?php

declare(strict_types=1);

namespace Modules\School\Http\Controllers\Api\Academic;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\School\Http\Controllers\Api\Common\BaseController;
use Modules\School\Models\Academic\AcademicYear;
use Modules\School\Models\Academic\Assessment;
use Modules\School\Models\Academic\Grade;
use Modules\School\Models\Academic\StudyGroup;
use Modules\School\Models\Institution\School;

class AssessmentController extends BaseController
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

    public function index(Request $request): JsonResponse
    {
        $schoolId = $this->resolveSchoolId($request);
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

        $perPage = min((int) ($request->input('per_page', 20)), 100);
        $assessments = $query->latest('date')->paginate($perPage);

        return $this->sendResponse($assessments, 'Assessments retrieved successfully');
    }

    public function store(Request $request): JsonResponse
    {
        $schoolId = $this->resolveSchoolId($request);

        $validated = $request->validate([
            'study_group_id' => 'required|exists:sch_acad_study_groups,id',
            'subject_id' => 'required|exists:sch_acad_subjects,id',
            'teacher_id' => 'required|exists:sch_hr_staff,id',
            'learning_objective_id' => 'nullable|exists:sch_acad_learning_objectives,id',
            'title' => 'required|string|max:150',
            'type' => 'required|in:formative,summative_material,summative_final',
            'weight' => 'integer|min:1|max:10',
            'max_score' => 'integer|min:10|max:100',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $activeYear = AcademicYear::where('school_id', $schoolId)
            ->where('is_active', true)
            ->with('activeSemester')
            ->first();

        $academicYearId = $activeYear ? $activeYear->id : 1;
        $semesterId = $activeYear && $activeYear->activeSemester ? $activeYear->activeSemester->id : 1;

        $assessment = Assessment::create(array_merge($validated, [
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
        $students = $assessment->studyGroup ? $assessment->studyGroup->students : collect();

        $existingGrades = Grade::where('assessment_id', $id)
            ->get()
            ->keyBy('student_id');

        $result = $students->map(function ($student) use ($existingGrades) {
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

    public function storeBulkGrades(Request $request, int $id): JsonResponse
    {
        $assessment = Assessment::findOrFail($id);

        $validated = $request->validate([
            'grades' => 'required|array|min:1',
            'grades.*.student_id' => 'required|exists:sch_std_students,id',
            'grades.*.score' => 'required|numeric|min:0|max:100',
            'grades.*.remedial_score' => 'nullable|numeric|min:0|max:100',
            'grades.*.notes' => 'nullable|string|max:500',
        ]);

        foreach ($validated['grades'] as $item) {
            Grade::updateOrCreate(
                [
                    'assessment_id' => $assessment->id,
                    'student_id' => $item['student_id'],
                ],
                [
                    'score' => $item['score'],
                    'remedial_score' => $item['remedial_score'] ?? null,
                    'notes' => $item['notes'] ?? null,
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
