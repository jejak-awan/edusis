<?php

declare(strict_types=1);

namespace Modules\School\Services\Attendance;

use Illuminate\Support\Facades\DB;
use Modules\School\Models\Academic\AcademicYear;
use Modules\School\Models\Academic\Attendance;
use Modules\School\Models\Academic\StudyGroup;
use Modules\School\Models\HR\Staff;
use Modules\School\Models\HR\StaffAttendance;
use Modules\School\Models\HR\StaffShift;

class AttendanceService
{
    /**
     * Simpan presensi massal siswa dalam satu rombel untuk tanggal tertentu.
     */
    public function saveBulkStudentAttendance(
        int $studyGroupId,
        string $date,
        array $records,
        string|int|null $recorderId = null
    ): void {
        $group = StudyGroup::findOrFail($studyGroupId);

        $activeYear = AcademicYear::where('school_id', $group->school_id)
            ->where('is_active', true)
            ->with('activeSemester')
            ->first();

        $academicYearId = $activeYear ? $activeYear->id : $group->academic_year_id;
        $semesterId = $activeYear?->activeSemester?->id;

        DB::transaction(function () use ($records, $group, $date, $recorderId, $academicYearId, $semesterId) {
            foreach ($records as $record) {
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
    }

    /**
     * Catat check-in presensi GTK/pegawai dengan kalkulasi toleransi shift kerja.
     */
    public function recordStaffCheckIn(
        int $staffId,
        ?float $latitude = null,
        ?float $longitude = null,
        ?string $deviceInfo = null,
        ?string $notes = null
    ): StaffAttendance {
        $staff = Staff::findOrFail($staffId);
        $date = now()->toDateString();
        $nowTime = now()->format('H:i:s');

        $shift = StaffShift::where('school_id', $staff->school_id)->where('is_active', true)->first();

        $status = 'hadir';
        $lateMinutes = 0;

        if ($shift) {
            $shiftStartTime = strtotime($shift->start_time);
            $checkInTime = strtotime($nowTime);
            $graceMinutes = (int) ($shift->grace_period_minutes ?? config('school.attendance.staff_grace_period_minutes', 15));
            $graceEndTime = $shiftStartTime + ($graceMinutes * 60);

            if ($checkInTime > $graceEndTime) {
                $status = 'terlambat';
                $lateMinutes = (int) round(($checkInTime - $shiftStartTime) / 60);
            }
        }

        return StaffAttendance::updateOrCreate(
            [
                'staff_id' => $staff->id,
                'date' => $date,
            ],
            [
                'shift_id' => $shift?->id,
                'check_in' => $nowTime,
                'status' => $status,
                'late_minutes' => $lateMinutes,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'device_info' => $deviceInfo,
                'notes' => $notes,
            ]
        );
    }
}
