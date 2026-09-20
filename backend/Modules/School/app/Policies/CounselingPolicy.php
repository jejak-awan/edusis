<?php

declare(strict_types=1);

namespace Modules\School\Policies;

use Modules\Core\System\Models\User;
use Modules\School\Models\Counseling\CounselingCase;

/**
 * Kebijakan otorisasi data Bimbingan Konseling (BK).
 * Menegakkan perlindungan ketat terhadap kasus berkategori privat/confidential.
 */
class CounselingPolicy
{
    /**
     * Otorisasi melihat daftar kasus bimbingan konseling.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission(['view counseling', 'manage counseling']);
    }

    /**
     * Otorisasi melihat detail kasus bimbingan konseling.
     * Jika kasus bertanda 'confidential' atau 'private', HANYA guru BK dan kepala sekolah yang diizinkan.
     */
    public function view(User $user, CounselingCase $case): bool
    {
        // Kasus rahasia / privat
        if (in_array($case->confidentiality, ['confidential', 'private', 'strictly_confidential'], true)) {
            // Guru BK yang menangani kasus atau user dengan izin manajerial BK
            if ($user->hasPermissionTo('manage counseling')) {
                return true;
            }

            return $user->hasRole('kepala-sekolah');
        }

        return $user->hasAnyPermission(['view counseling', 'manage counseling']);
    }

    /**
     * Otorisasi membuat sesi/kasus konseling baru.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyPermission(['view counseling', 'manage counseling']);
    }

    /**
     * Otorisasi memperbarui catatan konseling.
     */
    public function update(User $user, CounselingCase $case): bool
    {
        return $user->hasPermissionTo('manage counseling');
    }

    /**
     * Otorisasi menghapus catatan konseling.
     */
    public function delete(User $user, CounselingCase $case): bool
    {
        return $user->hasPermissionTo('manage counseling');
    }
}
