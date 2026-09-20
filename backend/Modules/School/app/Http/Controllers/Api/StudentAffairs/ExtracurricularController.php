<?php

declare(strict_types=1);

namespace Modules\School\Http\Controllers\Api\StudentAffairs;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\School\Http\Controllers\Api\Common\BaseController;
use Modules\School\Models\Institution\School;
use Modules\School\Models\StudentAffairs\Extracurricular;

class ExtracurricularController extends BaseController
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
        $query = Extracurricular::where('school_id', $schoolId)->with('advisor');

        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        if ($request->has('is_active')) {
            $query->where('is_active', filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN));
        }

        $extracurriculars = $query->orderBy('name')->get();

        return $this->sendResponse($extracurriculars, 'Extracurricular activities retrieved successfully');
    }

    public function store(Request $request): JsonResponse
    {
        $schoolId = $this->resolveSchoolId($request);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'category' => 'required|in:wajib,pilihan',
            'advisor_staff_id' => 'nullable|exists:sch_hr_staff,id',
            'schedule_day' => 'nullable|string|max:30',
            'location' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $ekskul = Extracurricular::create(array_merge($validated, [
            'school_id' => $schoolId,
            'is_active' => $validated['is_active'] ?? true,
        ]));

        return $this->sendResponse($ekskul, 'Extracurricular created successfully', 201);
    }

    public function show(int $id): JsonResponse
    {
        $ekskul = Extracurricular::with('advisor')->findOrFail($id);
        return $this->sendResponse($ekskul, 'Extracurricular details retrieved');
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $ekskul = Extracurricular::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:100',
            'category' => 'sometimes|in:wajib,pilihan',
            'advisor_staff_id' => 'nullable|exists:sch_hr_staff,id',
            'schedule_day' => 'nullable|string|max:30',
            'location' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'is_active' => 'sometimes|boolean',
        ]);

        $ekskul->update($validated);

        return $this->sendResponse($ekskul, 'Extracurricular updated successfully');
    }

    public function destroy(int $id): JsonResponse
    {
        $ekskul = Extracurricular::findOrFail($id);
        $ekskul->delete();

        return $this->sendResponse(null, 'Extracurricular deleted successfully');
    }
}
