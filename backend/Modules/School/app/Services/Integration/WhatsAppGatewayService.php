<?php

declare(strict_types=1);

namespace Modules\School\Services\Integration;

use Carbon\Carbon;
use Modules\School\Models\Academic\Attendance;
use Modules\School\Models\Communication\WhatsAppLog;
use Modules\School\Models\Institution\School;

class WhatsAppGatewayService
{
    /**
     * Normalisasi nomor telepon ke format internasional E.164 Indonesia (62...).
     */
    public function normalizePhoneNumber(string $phoneNumber): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phoneNumber) ?? '';

        if (str_starts_with($phone, '0')) {
            return '62'.substr($phone, 1);
        }

        if (! str_starts_with($phone, '62') && strlen($phone) >= 9) {
            return '62'.$phone;
        }

        return $phone;
    }

    /**
     * Kirim notifikasi alert presensi ke orang tua / wali siswa secara otomatis.
     */
    public function sendAttendanceAlerts(int $schoolId, string $date, array $inputStatuses = ['A']): array
    {
        $statusMap = [
            'alpa' => 'A', 'sakit' => 'S', 'izin' => 'I', 'hadir' => 'H',
            'a' => 'A', 's' => 'S', 'i' => 'I', 'h' => 'H',
            'A' => 'A', 'S' => 'S', 'I' => 'I', 'H' => 'H',
        ];

        $statuses = array_values(array_unique(array_filter(array_map(
            fn ($s) => $statusMap[(string) $s] ?? (string) $s,
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
        $fallbackName = config('app.name', 'EduSIS');
        $schoolName = $school?->name ?? (is_string($fallbackName) ? $fallbackName : 'EduSIS');

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

            $phone = $this->normalizePhoneNumber($recipientPhone);

            $alreadySent = WhatsAppLog::where('school_id', $schoolId)
                ->where('student_id', $student->id)
                ->where('message_type', 'attendance_alert')
                ->whereDate('sent_at', $date)
                ->exists();

            if ($alreadySent) {
                $skippedCount++;

                continue;
            }

            $statusLabel = $statusLabels[$att->status] ?? $att->status;
            $groupName = $att->studyGroup?->name ?? '-';

            $message = sprintf(
                "Yth. %s,\n\nKami menginformasikan bahwa putra/putri Anda:\nNama: *%s*\nKelas: *%s*\nTanggal: *%s*\nStatus Presensi: *%s*\n\nMohon konfirmasi atau hubungi pihak sekolah jika terdapat kekeliruan.\nTerima kasih.\n\n— *%s*",
                $recipientName,
                $student->full_name,
                $groupName,
                Carbon::parse($date)->translatedFormat('d F Y'),
                $statusLabel,
                $schoolName
            );

            $log = WhatsAppLog::create([
                'school_id' => $schoolId,
                'student_id' => $student->id,
                'recipient_phone' => $phone,
                'recipient_name' => $recipientName,
                'message_type' => 'attendance_alert',
                'content' => $message,
                'status' => 'sent',
                'sent_at' => now(),
                'gateway_response' => [
                    'provider' => 'internal_mock',
                    'status' => 'success',
                    'message_id' => 'WA-'.uniqid(),
                    'timestamp' => now()->toIso8601String(),
                ],
            ]);

            $createdLogs[] = $log;
            $sentCount++;
        }

        return [
            'total_absences' => $attendances->count(),
            'sent' => $sentCount,
            'skipped' => $skippedCount,
            'logs' => $createdLogs,
        ];
    }
}
