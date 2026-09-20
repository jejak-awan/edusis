<?php

declare(strict_types=1);

namespace Modules\School\Http\Controllers\Api\Academic;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\School\Http\Controllers\Api\Common\BaseController;
use Modules\School\Models\Academic\AcademicYear;
use Modules\School\Models\Academic\Attendance;
use Modules\School\Models\Academic\StudyGroup;
use Modules\School\Models\HR\Staff;
use Modules\School\Models\HR\StaffAttendance;
use Modules\School\Models\HR\StaffShift;
use Modules\School\Models\Institution\School;

class AttendanceController extends BaseController
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
                'status' => $att ? $att->status : 'H', // Default to Hadir
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

    public function saveBulkStudentAttendance(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'study_group_id' => 'required|exists:sch_acad_study_groups,id',
            'date' => 'required|date',
            'records' => 'required|array|min:1',
            'records.*.student_id' => 'required|exists:sch_std_students,id',
            'records.*.status' => 'required|in:H,I,S,A',
            'records.*.notes' => 'nullable|string',
        ]);

        $group = StudyGroup::findOrFail($validated['study_group_id']);
        $date = $validated['date'];
        $user = $request->user();
        $recorderId = $user ? $user->id : null;

        $activeYear = AcademicYear::where('school_id', $group->school_id)
            ->where('is_active', true)
            ->with('activeSemester')
            ->first();

        $academicYearId = $activeYear ? $activeYear->id : $group->academic_year_id;
        $semesterId = $activeYear && $activeYear->activeSemester ? $activeYear->activeSemester->id : null;

        DB::transaction(function () use ($validated, $group, $date, $recorderId, $academicYearId, $semesterId) {
            foreach ($validated['records'] as $record) {
                $att = Attendance::where('student_id', $record['student_id'])
                    ->whereDate('date', $date)
                    ->first();

                $payload = [
                    'study_group_id' => $group->id,
                    'academic_year_id' => $academicYearId,
                    'semester_id' => $semesterId,
                    'status' => $record['status'],
                    'notes' => $record['notes'] ?? null,
                    'recorded_by' => $recorderId,
                ];

                if ($att) {
                    $att->update($payload);
                } else {
                    Attendance::create(array_merge($payload, [
                        'student_id' => $record['student_id'],
                        'date' => $date,
                    ]));
                }
            }
        });

        return $this->sendResponse(null, 'Student attendances saved successfully');
    }

    // --- Staff Attendance ---

    public function getStaffAttendance(Request $request): JsonResponse
    {
        $schoolId = $this->resolveSchoolId($request);
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

    public function recordStaffCheckIn(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'staff_id' => 'required|exists:sch_hr_staff,id',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'device_info' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $date = now()->toDateString();
        $nowTime = now()->format('H:i:s');

        // Check active shift
        $staff = Staff::findOrFail($validated['staff_id']);
        $shift = StaffShift::where('school_id', $staff->school_id)->where('is_active', true)->first();

        $status = 'hadir';
        $lateMinutes = 0;

        if ($shift) {
            $shiftStartTime = strtotime($shift->start_time);
            $checkInTime = strtotime($nowTime);
            $graceEndTime = $shiftStartTime + ($shift->grace_period_minutes * 60);

            if ($checkInTime > $graceEndTime) {
                $status = 'terlambat';
                $lateMinutes = (int) round(($checkInTime - $shiftStartTime) / 60);
            }
        }

        $att = StaffAttendance::updateOrCreate(
            [
                'staff_id' => $staff->id,
                'date' => $date,
            ],
            [
                'shift_id' => $shift ? $shift->id : null,
                'check_in' => $nowTime,
                'status' => $status,
                'late_minutes' => $lateMinutes,
                'latitude' => $validated['latitude'] ?? null,
                'longitude' => $validated['longitude'] ?? null,
                'device_info' => $validated['device_info'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]
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
