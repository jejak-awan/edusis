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
use Modules\School\Models\Student\Student;
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

        $group = StudyGroup::with('students')->findOrFail((int) $validated['study_group_id']);
        assert($group instanceof StudyGroup);
        $date = (string) $validated['date'];

        $existingAttendances = Attendance::where('study_group_id', $group->id)
            ->where('date', $date)
            ->get()
            ->keyBy('student_id');

        $result = $group->students->map(function (Student $student) use ($existingAttendances): array {
            /** @var Attendance|null $att */
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

        $studyGroupId = isset($validated['study_group_id']) && is_numeric($validated['study_group_id']) ? (int) $validated['study_group_id'] : 0;
        $date = isset($validated['date']) && is_string($validated['date']) ? $validated['date'] : now()->toDateString();
        /** @var array<int, array<string, mixed>> $records */
        $records = isset($validated['records']) && is_array($validated['records']) ? $validated['records'] : [];

        $this->attendanceService->saveBulkStudentAttendance(
            $studyGroupId,
            $date,
            $records,
            $recorderId
        );

        return $this->sendResponse(null, 'Student attendances saved successfully');
    }

    // --- Staff Attendance ---

    public function getStaffAttendance(Request $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();
        $date = $request->string('date', now()->toDateString())->value();

        $staffList = Staff::where('school_id', $schoolId)
            ->where('is_active', true)
            ->with(['attendances' => function ($q) use ($date) {
                $q->where('date', $date);
            }])
            ->get();

        $result = $staffList->map(function (Staff $staff): array {
            /** @var StaffAttendance|null $att */
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

        $staffId = isset($validated['staff_id']) && is_numeric($validated['staff_id']) ? (int) $validated['staff_id'] : 0;
        $lat = isset($validated['latitude']) && is_numeric($validated['latitude']) ? (float) $validated['latitude'] : null;
        $lng = isset($validated['longitude']) && is_numeric($validated['longitude']) ? (float) $validated['longitude'] : null;
        $deviceInfo = isset($validated['device_info']) && is_string($validated['device_info']) ? $validated['device_info'] : null;
        $notes = isset($validated['notes']) && is_string($validated['notes']) ? $validated['notes'] : null;

        $att = $this->attendanceService->recordStaffCheckIn(
            $staffId,
            $lat,
            $lng,
            $deviceInfo,
            $notes
        );

        return $this->sendResponse($att, 'Check-in recorded successfully');
    }

    public function recordStaffCheckOut(Request $request): JsonResponse
    {
        $request->validate([
            'staff_id' => 'required|exists:sch_hr_staff,id',
        ]);

        $staffId = $request->integer('staff_id');
        $date = now()->toDateString();
        $nowTime = now()->format('H:i:s');

        $att = StaffAttendance::where('staff_id', $staffId)
            ->where('date', $date)
            ->firstOrFail();
        assert($att instanceof StaffAttendance);

        $att->update(['check_out' => $nowTime]);

        return $this->sendResponse($att, 'Check-out recorded successfully');
    }
}
