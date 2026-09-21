<?php

declare(strict_types=1);

namespace Modules\School\Http\Controllers\Api\Student;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\School\Http\Controllers\Api\Common\BaseController;
use Modules\School\Http\Requests\Student\StorePointCategoryRequest;
use Modules\School\Http\Requests\Student\StoreViolationRequest;
use Modules\School\Models\Student\Achievement;
use Modules\School\Models\Student\PointCategory;
use Modules\School\Models\Student\Violation;
use Modules\School\Services\Student\DisciplineService;

class DisciplineController extends BaseController
{
    public function __construct(
        protected DisciplineService $disciplineService
    ) {}

    // --- Point Categories ---

    public function getCategories(Request $request): JsonResponse
    {
        $query = PointCategory::query();

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->has('is_active')) {
            $query->where('is_active', filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN));
        }

        $categories = $query->orderBy('type')->orderBy('name')->get();

        return $this->sendResponse($categories, 'Point categories retrieved');
    }

    public function storeCategory(StorePointCategoryRequest $request): JsonResponse
    {
        $category = PointCategory::create($request->validated());

        return $this->sendResponse($category, 'Point category created', 201);
    }

    // --- Record Credit Point Transaction ---

    public function recordPoint(StoreViolationRequest $request): JsonResponse
    {
        $reporterId = $request->user()?->id;
        $result = $this->disciplineService->recordPoint($request->validated(), $reporterId);

        return $this->sendResponse($result, 'Discipline point recorded successfully', 201);
    }

    // --- Violations List ---

    public function getViolations(Request $request): JsonResponse
    {
        $query = Violation::with(['student.department', 'creditPoint.category']);

        if ($request->filled('sp_level')) {
            $query->where('sp_level', $request->input('sp_level'));
        }

        if ($request->has('is_resolved')) {
            $query->where('is_resolved', filter_var($request->input('is_resolved'), FILTER_VALIDATE_BOOLEAN));
        }

        $perPage = min($request->integer('per_page', 20), 100);
        $violations = $query->latest('violation_date')->paginate($perPage);

        return $this->sendResponse($violations, 'Violations retrieved');
    }

    public function resolveViolation(Request $request, int $id): JsonResponse
    {
        $violation = Violation::findOrFail($id);
        $violation->update([
            'is_resolved' => true,
            'follow_up_notes' => $request->input('follow_up_notes', 'Telah diselesaikan / dibina oleh guru BK & Kesiswaan.'),
        ]);

        return $this->sendResponse($violation, 'Violation resolved');
    }

    // --- Achievements List ---

    public function getAchievements(Request $request): JsonResponse
    {
        $query = Achievement::with(['student.department', 'creditPoint']);

        if ($request->filled('level')) {
            $query->where('level', $request->input('level'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        $perPage = min($request->integer('per_page', 20), 100);
        $achievements = $query->latest('event_date')->paginate($perPage);

        return $this->sendResponse($achievements, 'Achievements retrieved');
    }
}
