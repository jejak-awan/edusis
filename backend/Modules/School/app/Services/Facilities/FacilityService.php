<?php

declare(strict_types=1);

namespace Modules\School\Services\Facilities;

use Modules\School\Models\Facilities\Asset;
use Modules\School\Models\Facilities\MaintenanceTicket;
use Modules\School\Models\Facilities\Room;

class FacilityService
{
    /**
     * Daftarkan ruangan / laboratorium / bengkel baru sekolah.
     *
     * @param array<string, mixed> $data
     */
    public function createRoom(array $data, int $schoolId): Room
    {
        return Room::create(array_merge($data, [
            'school_id' => $schoolId,
            'capacity' => $data['capacity'] ?? 36,
            'floor' => $data['floor'] ?? 1,
        ]));
    }

    /**
     * Daftarkan aset sarana dan prasarana.
     *
     * @param array<string, mixed> $data
     */
    public function createAsset(array $data, int $schoolId): Asset
    {
        return Asset::create(array_merge($data, [
            'school_id' => $schoolId,
        ]));
    }

    /**
     * Laporkan tiket kendala / kerusakan sarpras.
     *
     * @param array<string, mixed> $data
     */
    public function reportMaintenanceTicket(array $data, int $schoolId): MaintenanceTicket
    {
        $year = (int) date('Y');
        $count = MaintenanceTicket::where('school_id', $schoolId)->whereYear('created_at', $year)->count();
        $ticketNumber = sprintf('TKT/%d/%04d', $year, $count + 1);

        return MaintenanceTicket::create(array_merge($data, [
            'school_id' => $schoolId,
            'ticket_number' => $ticketNumber,
            'status' => 'open',
            'cost' => $data['cost'] ?? 0,
        ]));
    }

    /**
     * Catat penanganan dan penutupan tiket perbaikan sarpras.
     *
     * @param array<string, mixed> $data
     */
    public function resolveMaintenanceTicket(MaintenanceTicket $ticket, array $data): MaintenanceTicket
    {
        $status = $data['status'] ?? 'resolved';
        $updateData = [
            'status' => $status,
            'resolution_notes' => $data['resolution_notes'],
        ];

        if (isset($data['cost'])) {
            $updateData['cost'] = $data['cost'];
        }

        if ($status === 'resolved') {
            $updateData['resolved_at'] = now();
        }

        $ticket->update($updateData);

        return $ticket;
    }
}
