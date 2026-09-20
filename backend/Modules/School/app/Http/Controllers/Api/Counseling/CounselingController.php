<?php

declare(strict_types=1);

namespace Modules\School\Http\Controllers\Api\Counseling;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\School\Http\Controllers\Api\Common\BaseController;
use Modules\School\Models\Academic\AcademicYear;
use Modules\School\Models\Counseling\CounselingCase;
use Modules\School\Models\Institution\School;

class CounselingController extends BaseController
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
        $query = CounselingCase::where('school_id', $schoolId)
            ->with(['student.studyGroups', 'counselor', 'violation']);

        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->input('student_id'));
        }

        if ($request->filled('confidentiality')) {
            $query->where('confidentiality', $request->input('confidentiality'));
        }

        $perPage = min((int) ($request->input('per_page', 20)), 100);
        $cases = $query->latest('date')->paginate($perPage);

        return $this->sendResponse($cases, 'Counseling cases retrieved successfully');
    }

    public function store(Request $request): JsonResponse
    {
        $schoolId = $this->resolveSchoolId($request);

        $validated = $request->validate([
            'student_id' => 'required|exists:sch_std_students,id',
            'counselor_id' => 'required|exists:sch_hr_staff,id',
            'violation_id' => 'nullable|exists:sch_std_violations,id',
            'date' => 'required|date',
            'category' => 'required|in:pribadi,sosial,belajar,karir',
            'confidentiality' => 'required|in:biasa,internal,rahasia,sangat_rahasia',
            'problem_description' => 'required|string|max:2000',
            'counseling_steps' => 'nullable|string',
            'solution_plan' => 'nullable|string',
            'follow_up_notes' => 'nullable|string',
            'status' => 'in:open,in_progress,resolved,referred',
        ]);

        $activeYear = AcademicYear::where('school_id', $schoolId)
            ->where('is_active', true)
            ->with('activeSemester')
            ->first();

        $academicYearId = $activeYear ? $activeYear->id : 1;
        $semesterId = $activeYear && $activeYear->activeSemester ? $activeYear->activeSemester->id : 1;

        $countToday = CounselingCase::where('school_id', $schoolId)
            ->whereDate('created_at', now()->toDateString())
            ->count();

        $caseNumber = sprintf('BK/%s/%03d', now()->format('Y/m'), $countToday + 1);

        $case = CounselingCase::create(array_merge($validated, [
            'school_id' => $schoolId,
            'academic_year_id' => $academicYearId,
            'semester_id' => $semesterId,
            'case_number' => $caseNumber,
            'status' => $validated['status'] ?? 'open',
        ]));

        return $this->sendResponse($case, 'Counseling case recorded successfully', 201);
    }

    public function show(int $id): JsonResponse
    {
        $case = CounselingCase::with([
            'student.studyGroups',
            'counselor',
            'violation.creditPoint',
            'academicYear',
            'semester',
        ])->findOrFail($id);

        return $this->sendResponse($case, 'Counseling case details retrieved');
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $case = CounselingCase::findOrFail($id);

        $validated = $request->validate([
            'counselor_id' => 'sometimes|exists:sch_hr_staff,id',
            'category' => 'sometimes|in:pribadi,sosial,belajar,karir',
            'confidentiality' => 'sometimes|in:biasa,internal,rahasia,sangat_rahasia',
            'problem_description' => 'sometimes|string',
            'counseling_steps' => 'nullable|string',
            'solution_plan' => 'nullable|string',
            'follow_up_notes' => 'nullable|string',
            'status' => 'sometimes|in:open,in_progress,resolved,referred',
        ]);

        $case->update($validated);

        return $this->sendResponse($case, 'Counseling case updated successfully');
    }

    public function resolve(Request $request, int $id): JsonResponse
    {
        $case = CounselingCase::findOrFail($id);

        $validated = $request->validate([
            'solution_plan' => 'nullable|string',
            'follow_up_notes' => 'nullable|string',
        ]);

        $case->update(array_merge($validated, [
            'status' => 'resolved',
        ]));

        return $this->sendResponse($case, 'Counseling case marked as resolved');
    }

    public function destroy(int $id): JsonResponse
    {
        $case = CounselingCase::findOrFail($id);
        $case->delete();

        return $this->sendResponse(null, 'Counseling case deleted successfully');
    }
}
