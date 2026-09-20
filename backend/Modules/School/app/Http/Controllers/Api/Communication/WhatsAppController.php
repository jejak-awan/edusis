<?php

declare(strict_types=1);

namespace Modules\School\Http\Controllers\Api\Communication;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\School\Http\Controllers\Api\Common\BaseController;
use Modules\School\Models\Academic\Attendance;
use Modules\School\Models\Communication\WhatsAppLog;
use Modules\School\Models\Institution\School;
use Modules\School\Services\Integration\WhatsAppGatewayService;

class WhatsAppController extends BaseController
{
    public function __construct(
        protected WhatsAppGatewayService $waService
    ) {}

    public function getLogs(Request $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();
        $query = WhatsAppLog::where('school_id', $schoolId)
            ->with('student:id,full_name,nisn');

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('message_type')) {
            $query->where('message_type', $request->input('message_type'));
        }

        if ($request->filled('search')) {
            $search = $request->string('search')->value();
            $query->where(function ($q) use ($search) {
                $q->where('recipient_phone', 'like', "%{$search}%")
                    ->orWhere('recipient_name', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%")
                    ->orWhereHas('student', function ($sq) use ($search) {
                        $sq->where('full_name', 'like', "%{$search}%")
                            ->orWhere('nisn', 'like', "%{$search}%");
                    });
            });
        }

        $perPage = $request->integer('per_page', 20);
        $logs = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return $this->sendResponse($logs, 'WhatsApp logs retrieved successfully');
    }

    public function getStats(Request $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();
        $base = WhatsAppLog::where('school_id', $schoolId);

        $stats = [
            'total' => (clone $base)->count(),
            'sent' => (clone $base)->where('status', 'sent')->count(),
            'failed' => (clone $base)->where('status', 'failed')->count(),
            'queued' => (clone $base)->where('status', 'queued')->count(),
            'delivered' => (clone $base)->where('status', 'delivered')->count(),
            'attendance_alerts' => (clone $base)->where('message_type', 'attendance_alert')->count(),
            'announcements' => (clone $base)->where('message_type', 'announcement')->count(),
        ];

        return $this->sendResponse($stats, 'WhatsApp gateway statistics retrieved');
    }

    public function sendMessage(Request $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();

        $validated = $request->validate([
            'recipient_phone' => 'required|string|max:30',
            'recipient_name' => 'required|string|max:100',
            'message_type' => 'required|in:attendance_alert,discipline_sp,achievement,announcement,general',
            'content' => 'required|string|max:2000',
            'student_id' => 'nullable|exists:sch_std_students,id',
        ]);

        $phone = $this->waService->normalizePhoneNumber($validated['recipient_phone']);

        $log = WhatsAppLog::create([
            'school_id' => $schoolId,
            'student_id' => $validated['student_id'] ?? null,
            'recipient_phone' => $phone,
            'recipient_name' => $validated['recipient_name'],
            'message_type' => $validated['message_type'],
            'content' => $validated['content'],
            'status' => 'sent',
            'sent_at' => Carbon::now(),
            'error_message' => null,
        ]);

        return $this->sendResponse($log, 'WhatsApp message dispatched successfully');
    }

    public function sendAttendanceAlerts(Request $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();
        $date = $request->string('date', Carbon::today()->toDateString())->value();
        $inputStatuses = (array) $request->input('statuses', ['A', 'alpa']);

        $statusMap = [
            'alpa' => 'A', 'sakit' => 'S', 'izin' => 'I', 'hadir' => 'H',
            'a' => 'A', 's' => 'S', 'i' => 'I', 'h' => 'H',
            'A' => 'A', 'S' => 'S', 'I' => 'I', 'H' => 'H',
        ];

        $statuses = array_values(array_unique(array_filter(array_map(
            function ($s) use ($statusMap): string {
                $str = is_scalar($s) ? (string) $s : '';
                return $statusMap[$str] ?? $str;
            },
            $inputStatuses
        ))));

        if (empty($statuses)) {
            $statuses = ['A'];
        }

        $attendances = Attendance::where('date', $date)
            ->whereIn('status', $statuses)
            ->with(['student.parent', 'studyGroup'])
            ->get();

        $sentCount = 0;
        $skippedCount = 0;
        $createdLogs = [];

        $school = School::find($schoolId);
        $schoolName = $school instanceof School ? $school->name : 'EduSIS';

        $statusLabels = [
            'A' => 'ALPA (Tanpa Keterangan)',
            'S' => 'SAKIT',
            'I' => 'IZIN',
            'H' => 'HADIR',
        ];

        foreach ($attendances as $att) {
            $student = $att->student;
            if (! $student) {
                continue;
            }

            $parent = $student->parent;
            $recipientPhone = $parent?->mother_phone
                ?: $parent?->father_phone
                ?: $parent?->guardian_phone
                ?: $parent?->emergency_phone
                ?: null;

            $recipientName = $parent?->mother_name
                ?: $parent?->father_name
                ?: $parent?->guardian_name
                ?: ('Orang Tua '.$student->full_name);

            if (! $recipientPhone) {
                $skippedCount++;

                continue;
            }

            $phone = $this->waService->normalizePhoneNumber($recipientPhone);

            $alreadySent = WhatsAppLog::where('school_id', $schoolId)
                ->where('student_id', $student->id)
                ->where('message_type', 'attendance_alert')
                ->whereDate('sent_at', $date)
                ->exists();

            if ($alreadySent) {
                $skippedCount++;

                continue;
            }

            $studyGroupName = $att->studyGroup instanceof \Modules\School\Models\Academic\StudyGroup ? $att->studyGroup->name : 'Kelas Siswa';
            $statusLabel = $statusLabels[$att->status] ?? strtoupper((string) $att->status);
            $formattedDate = Carbon::parse($date)->isoFormat('dddd, D MMMM Y');

            $message = "Yth. Bapak/Ibu Wali dari {$student->full_name} ({$studyGroupName}),\n\n"
                ."Kami menginformasikan bahwa siswa tercatat {$statusLabel} pada absensi sekolah tanggal {$formattedDate}.\n\n"
                ."Mohon konfirmasi atau hubungi pihak sekolah jika terdapat kekeliruan.\n\n"
                ."Salam hangat,\n{$schoolName}";

            $log = WhatsAppLog::create([
                'school_id' => $schoolId,
                'student_id' => $student->id,
                'recipient_phone' => $phone,
                'recipient_name' => $recipientName,
                'message_type' => 'attendance_alert',
                'content' => $message,
                'status' => 'sent',
                'sent_at' => Carbon::now(),
            ]);

            $createdLogs[] = $log;
            $sentCount++;
        }

        return $this->sendResponse([
            'date' => $date,
            'total_detected' => $attendances->count(),
            'sent_count' => $sentCount,
            'skipped_count' => $skippedCount,
            'recent_dispatches' => $createdLogs,
        ], "Successfully sent {$sentCount} attendance alerts via WhatsApp Gateway");
    }

    public function resendLog(Request $request, int $id): JsonResponse
    {
        $schoolId = $this->getSchoolId();
        $log = WhatsAppLog::where('school_id', $schoolId)->findOrFail($id);

        $log->update([
            'status' => 'sent',
            'sent_at' => Carbon::now(),
            'error_message' => null,
        ]);

        return $this->sendResponse($log, 'WhatsApp message re-sent successfully');
    }
}
