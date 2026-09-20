<?php

declare(strict_types=1);

namespace Modules\School\Policies;

use Modules\Core\System\Models\User;

/**
 * Kebijakan otorisasi fasilitas, ruangan, aset, dan tiket pemeliharaan sarpras.
 */
class SarprasResourcePolicy
{
    /**
     * Otorisasi melihat fasilitas dan aset sekolah.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission(['view facilities', 'manage facilities']);
    }

    /**
     * Otorisasi mengelola sarana dan prasarana.
     */
    public function manage(User $user): bool
    {
        return $user->hasPermissionTo('manage facilities');
    }
}
