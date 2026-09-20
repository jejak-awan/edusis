<?php

declare(strict_types=1);

namespace Modules\School\Http\Controllers\Api\Academic;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\School\Http\Controllers\Api\Common\BaseController;
use Modules\School\Http\Requests\Academic\SaveBulkAttendanceRequest;
use Modules\School\Http\Requests\HR\StaffCheckInRequest;
use Modules\School\Models\Academic\Attendance;
use Modules\School\Models\Academic\StudyGroup;
use Modules\School\Models\HR\Staff;
use Modules\School\Models\HR\StaffAttendance;
use Modules\School\Services\Attendance\AttendanceService;

class AttendanceController extends BaseController
{
    public function __construct(
        protected AttendanceService $attendanceService
    ) {}

    // --- Student Attendance ---

    public function getStudentAttendance(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'study_group_id' => 'required|exists:sch_acad_study_groups,id',
            'date' => 'required|date',
        ]);

        $group = StudyGroup::with('students')->findOrFail($validated['study_group_id']);
        $date = $validated['date'];

        $existingAttendances = Attendance::where('study_group_id', $group->id)
            ->where('date', $date)
            ->get()
            ->keyBy('student_id');

        $result = $group->students->map(function ($student) use ($existingAttendances) {
            $att = $existingAttendances->get($student->id);

            return [
                'student_id' => $student->id,
                'nisn' => $student->nisn,
                'full_name' => $student->full_name,
                'gender' => $student->gender,
                'status' => $att ? $att->status : 'H',
                'notes' => $att ? $att->notes : null,
                'attachment_path' => $att ? $att->attachment_path : null,
            ];
        });

        return $this->sendResponse([
            'study_group' => $group->only(['id', 'name']),
            'date' => $date,
            'attendances' => $result,
        ], 'Student attendance list retrieved');
    }

    public function saveBulkStudentAttendance(SaveBulkAttendanceRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $recorderId = $request->user()?->id;

        $this->attendanceService->saveBulkStudentAttendance(
            (int) $validated['study_group_id'],
            $validated['date'],
            $validated['records'],
            $recorderId
        );

        return $this->sendResponse(null, 'Student attendances saved successfully');
    }

    // --- Staff Attendance ---

    public function getStaffAttendance(Request $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();
        $date = $request->input('date', now()->toDateString());

        $staffList = Staff::where('school_id', $schoolId)
            ->where('is_active', true)
            ->with(['attendances' => function ($q) use ($date) {
                $q->where('date', $date);
            }])
            ->get();

        $result = $staffList->map(function ($staff) {
            $att = $staff->attendances->first();

            return [
                'staff_id' => $staff->id,
                'full_name' => $staff->formal_name,
                'nip' => $staff->nip,
                'role_title' => $staff->role_title,
                'status' => $att ? $att->status : 'alpa',
                'check_in' => $att ? $att->check_in : null,
                'check_out' => $att ? $att->check_out : null,
                'late_minutes' => $att ? $att->late_minutes : 0,
                'notes' => $att ? $att->notes : null,
            ];
        });

        return $this->sendResponse([
            'date' => $date,
            'attendances' => $result,
        ], 'Staff attendance list retrieved');
    }

    public function recordStaffCheckIn(StaffCheckInRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $att = $this->attendanceService->recordStaffCheckIn(
            (int) $validated['staff_id'],
            isset($validated['latitude']) ? (float) $validated['latitude'] : null,
            isset($validated['longitude']) ? (float) $validated['longitude'] : null,
            $validated['device_info'] ?? null,
            $validated['notes'] ?? null
        );

        return $this->sendResponse($att, 'Check-in recorded successfully');
    }

    public function recordStaffCheckOut(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'staff_id' => 'required|exists:sch_hr_staff,id',
        ]);

        $date = now()->toDateString();
        $nowTime = now()->format('H:i:s');

        $att = StaffAttendance::where('staff_id', $validated['staff_id'])
            ->where('date', $date)
            ->firstOrFail();

        $att->update(['check_out' => $nowTime]);

        return $this->sendResponse($att, 'Check-out recorded successfully');
    }
}
