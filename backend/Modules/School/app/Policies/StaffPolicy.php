<?php

declare(strict_types=1);

namespace Modules\School\Policies;

use Modules\Core\System\Models\User;
use Modules\School\Models\HR\Staff;

/**
 * Kebijakan otorisasi data tenaga pendidik dan kependidikan (GTK).
 */
class StaffPolicy
{
    /**
     * Otorisasi melihat daftar staf GTK.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission(['view staff', 'manage staff']);
    }

    /**
     * Otorisasi melihat detail profil staf.
     */
    public function view(User $user, Staff $staff): bool
    {
        if ($user->id === $staff->user_id) {
            return true;
        }

        return $user->hasAnyPermission(['view staff', 'manage staff']);
    }

    /**
     * Otorisasi mengelola data staf GTK.
     */
    public function manage(User $user): bool
    {
        return $user->hasPermissionTo('manage staff');
    }
}
