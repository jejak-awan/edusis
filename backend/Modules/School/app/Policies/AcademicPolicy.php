<?php

declare(strict_types=1);

namespace Modules\School\Policies;

use Modules\Core\System\Models\User;

/**
 * Kebijakan otorisasi data akademik, kurikulum, dan jadwal KBM.
 */
class AcademicPolicy
{
    /**
     * Otorisasi melihat master data akademik.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission(['view academic', 'manage academic']);
    }

    /**
     * Otorisasi mengelola struktur kurikulum dan jadwal KBM.
     */
    public function manage(User $user): bool
    {
        return $user->hasPermissionTo('manage academic');
    }

    /**
     * Otorisasi input dan rekap nilai rapor.
     */
    public function manageGrades(User $user): bool
    {
        return $user->hasAnyPermission(['manage grades', 'grade assignments']);
    }
}
