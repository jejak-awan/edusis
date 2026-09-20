<?php

declare(strict_types=1);

namespace Modules\School\Http\Controllers\Api\Academic;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\School\Http\Controllers\Api\Common\BaseController;
use Modules\School\Http\Requests\Academic\StoreScheduleRequest;
use Modules\School\Models\Academic\AcademicYear;
use Modules\School\Models\Academic\Schedule;

class ScheduleController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();
        $query = Schedule::where('school_id', $schoolId)
            ->with(['studyGroup', 'subject', 'teacher', 'academicYear']);

        if ($request->filled('study_group_id')) {
            $query->where('study_group_id', $request->input('study_group_id'));
        }

        if ($request->filled('teacher_id')) {
            $query->where('teacher_id', $request->input('teacher_id'));
        }

        if ($request->filled('day_of_week')) {
            $query->where('day_of_week', $request->input('day_of_week'));
        }

        $schedules = $query->orderBy('day_of_week')->orderBy('start_time')->get();

        return $this->sendResponse($schedules, 'Schedules retrieved');
    }

    public function store(StoreScheduleRequest $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();
        $activeYear = AcademicYear::where('school_id', $schoolId)
            ->where('is_active', true)
            ->with('activeSemester')
            ->first();

        $academicYearId = $activeYear ? $activeYear->id : 1;
        $semesterId = $activeYear && $activeYear->activeSemester ? $activeYear->activeSemester->id : 1;

        $schedule = Schedule::create(array_merge($request->validated(), [
            'school_id' => $schoolId,
            'academic_year_id' => $academicYearId,
            'semester_id' => $semesterId,
        ]));

        return $this->sendResponse(
            $schedule->load(['studyGroup', 'subject', 'teacher']),
            'Schedule slot created successfully',
            201
        );
    }

    public function update(StoreScheduleRequest $request, int $id): JsonResponse
    {
        $schedule = Schedule::findOrFail($id);
        $schedule->update($request->validated());

        return $this->sendResponse($schedule->load(['studyGroup', 'subject', 'teacher']), 'Schedule slot updated');
    }

    public function destroy(int $id): JsonResponse
    {
        $schedule = Schedule::findOrFail($id);
        $schedule->delete();

        return $this->sendResponse(null, 'Schedule slot deleted');
    }
}
