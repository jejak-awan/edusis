<?php

declare(strict_types=1);

namespace Modules\School\Services\Operations;

use Modules\School\Models\Administration\GuestBook;

class GuestBookService
{
    /**
     * Catat kedatangan tamu sekolah dan terbitkan nomor badge pengenal.
     */
    public function checkIn(array $data, int $schoolId): GuestBook
    {
        if (empty($data['badge_number'])) {
            $countToday = GuestBook::where('school_id', $schoolId)
                ->whereDate('check_in_at', now()->toDateString())
                ->count();
            $data['badge_number'] = sprintf('TAMU-%02d', $countToday + 1);
        }

        return GuestBook::create(array_merge($data, [
            'school_id' => $schoolId,
            'check_in_at' => now(),
            'status' => 'checked_in',
        ]));
    }

    /**
     * Catat kepulangan tamu dan pemulangan lencana pengenal.
     */
    public function checkOut(GuestBook $guest): GuestBook
    {
        $guest->update([
            'check_out_at' => now(),
            'status' => 'checked_out',
        ]);

        return $guest;
    }
}
