<?php

declare(strict_types=1);

namespace Modules\School\Http\Controllers\Api\Counseling;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\School\Http\Controllers\Api\Common\BaseController;
use Modules\School\Http\Requests\Counseling\StoreCounselingCaseRequest;
use Modules\School\Http\Requests\Counseling\UpdateCounselingCaseRequest;
use Modules\School\Models\Counseling\CounselingCase;
use Modules\School\Services\Student\CounselingService;

class CounselingController extends BaseController
{
    public function __construct(
        protected CounselingService $counselingService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();
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

    public function store(StoreCounselingCaseRequest $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();
        $case = $this->counselingService->createCase($request->validated(), $schoolId);

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

    public function update(UpdateCounselingCaseRequest $request, int $id): JsonResponse
    {
        $case = CounselingCase::findOrFail($id);
        $updated = $this->counselingService->updateCase($case, $request->validated());

        return $this->sendResponse($updated, 'Counseling case updated successfully');
    }

    public function resolve(Request $request, int $id): JsonResponse
    {
        $case = CounselingCase::findOrFail($id);
        $validated = $request->validate([
            'solution_plan' => 'nullable|string',
            'follow_up_notes' => 'nullable|string',
        ]);

        $resolved = $this->counselingService->resolveCase($case, $validated);

        return $this->sendResponse($resolved, 'Counseling case marked as resolved');
    }

    public function destroy(int $id): JsonResponse
    {
        $case = CounselingCase::findOrFail($id);
        $case->delete();

        return $this->sendResponse(null, 'Counseling case deleted successfully');
    }
}
