<?php

declare(strict_types=1);

namespace Modules\School\Policies;

use Modules\Core\System\Models\User;

/**
 * Kebijakan otorisasi agenda pemilihan digital / E-Voting OSIS.
 */
class VotingPolicy
{
    /**
     * Otorisasi melihat agenda pemilihan.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission(['view voting', 'manage voting']);
    }

    /**
     * Otorisasi manajemen event, kandidat, dan generate token pemilih DPT.
     */
    public function manage(User $user): bool
    {
        return $user->hasPermissionTo('manage voting');
    }
}
