<?php

declare(strict_types=1);

namespace Modules\School\Http\Controllers\Api\Academic;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\School\Http\Controllers\Api\Common\BaseController;
use Modules\School\Models\Academic\AcademicYear;
use Modules\School\Models\Academic\P5Assessment;
use Modules\School\Models\Academic\P5Project;
use Modules\School\Models\Institution\School;

class P5ProjectController extends BaseController
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
        $query = P5Project::where('school_id', $schoolId)
            ->with(['coordinator', 'academicYear'])
            ->withCount('assessments');

        if ($request->filled('phase')) {
            $query->where('phase', $request->input('phase'));
        }

        if ($request->filled('theme')) {
            $query->where('theme', 'ilike', '%' . $request->input('theme') . '%');
        }

        $projects = $query->latest()->paginate(20);

        return $this->sendResponse($projects, 'P5 Projects retrieved successfully');
    }

    public function store(Request $request): JsonResponse
    {
        $schoolId = $this->resolveSchoolId($request);

        $validated = $request->validate([
            'coordinator_id' => 'nullable|exists:sch_hr_staff,id',
            'title' => 'required|string|max:200',
            'theme' => 'required|string|max:100',
            'phase' => 'required|in:E,F',
            'description' => 'nullable|string',
        ]);

        $activeYear = AcademicYear::where('school_id', $schoolId)
            ->where('is_active', true)
            ->first();

        $academicYearId = $activeYear ? $activeYear->id : 1;

        $project = P5Project::create(array_merge($validated, [
            'school_id' => $schoolId,
            'academic_year_id' => $academicYearId,
        ]));

        return $this->sendResponse($project, 'P5 Project created successfully', 201);
    }

    public function show(int $id): JsonResponse
    {
        $project = P5Project::with([
            'coordinator',
            'academicYear',
            'assessments.student',
        ])->findOrFail($id);

        return $this->sendResponse($project, 'P5 Project details retrieved');
    }

    public function storeBulkAssessments(Request $request, int $id): JsonResponse
    {
        $project = P5Project::findOrFail($id);

        $validated = $request->validate([
            'assessments' => 'required|array|min:1',
            'assessments.*.student_id' => 'required|exists:sch_std_students,id',
            'assessments.*.dimension' => 'required|string|max:80',
            'assessments.*.predicate' => 'required|in:MB,SB,BSH,SAB',
            'assessments.*.notes' => 'nullable|string',
        ]);

        foreach ($validated['assessments'] as $item) {
            P5Assessment::updateOrCreate(
                [
                    'project_id' => $project->id,
                    'student_id' => $item['student_id'],
                    'dimension' => $item['dimension'],
                ],
                [
                    'predicate' => $item['predicate'],
                    'notes' => $item['notes'] ?? null,
                ]
            );
        }

        return $this->sendResponse(null, 'P5 Assessments saved successfully');
    }

    public function destroy(int $id): JsonResponse
    {
        $project = P5Project::findOrFail($id);
        $project->delete();

        return $this->sendResponse(null, 'P5 Project deleted successfully');
    }
}
