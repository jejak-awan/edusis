<?php

declare(strict_types=1);

namespace Modules\School\Policies;

use Modules\Core\System\Models\User;
use Modules\School\Models\Student\Student;

/**
 * Kebijakan otorisasi akses data siswa (Dapodik).
 */
class StudentPolicy
{
    /**
     * Otorisasi melihat daftar siswa.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission(['view students', 'manage students']);
    }

    /**
     * Otorisasi melihat detail profil siswa.
     */
    public function view(User $user, Student $student): bool
    {
        if ($user->id === $student->user_id) {
            return true;
        }

        return $user->hasAnyPermission(['view students', 'manage students']);
    }

    /**
     * Otorisasi mendaftarkan siswa baru.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('manage students');
    }

    /**
     * Otorisasi memperbarui data siswa.
     */
    public function update(User $user, Student $student): bool
    {
        return $user->hasPermissionTo('manage students');
    }

    /**
     * Otorisasi menghapus siswa.
     */
    public function delete(User $user, Student $student): bool
    {
        return $user->hasPermissionTo('manage students');
    }
}
