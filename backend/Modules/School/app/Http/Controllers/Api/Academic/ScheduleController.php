<?php

declare(strict_types=1);

namespace Modules\School\Http\Controllers\Api\Academic;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\School\Http\Controllers\Api\Common\BaseController;
use Modules\School\Models\Academic\AcademicYear;
use Modules\School\Models\Academic\Schedule;
use Modules\School\Models\Institution\School;

class ScheduleController extends BaseController
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

    public function store(Request $request): JsonResponse
    {
        $schoolId = $this->resolveSchoolId($request);

        $validated = $request->validate([
            'study_group_id' => 'required|exists:sch_acad_study_groups,id',
            'subject_id' => 'required|exists:sch_acad_subjects,id',
            'teacher_id' => 'required|exists:sch_hr_staff,id',
            'day_of_week' => 'required|integer|min:1|max:7',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'room_name' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        $activeYear = AcademicYear::where('school_id', $schoolId)
            ->where('is_active', true)
            ->with('activeSemester')
            ->first();

        $academicYearId = $activeYear ? $activeYear->id : 1;
        $semesterId = $activeYear && $activeYear->activeSemester ? $activeYear->activeSemester->id : 1;

        $schedule = Schedule::create(array_merge($validated, [
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

    public function update(Request $request, int $id): JsonResponse
    {
        $schedule = Schedule::findOrFail($id);

        $validated = $request->validate([
            'study_group_id' => 'required|exists:sch_acad_study_groups,id',
            'subject_id' => 'required|exists:sch_acad_subjects,id',
            'teacher_id' => 'required|exists:sch_hr_staff,id',
            'day_of_week' => 'required|integer|min:1|max:7',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'room_name' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        $schedule->update($validated);

        return $this->sendResponse($schedule->load(['studyGroup', 'subject', 'teacher']), 'Schedule slot updated');
    }

    public function destroy(int $id): JsonResponse
    {
        $schedule = Schedule::findOrFail($id);
        $schedule->delete();

        return $this->sendResponse(null, 'Schedule slot deleted');
    }
}
