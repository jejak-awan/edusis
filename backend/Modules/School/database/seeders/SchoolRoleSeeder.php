<?php

declare(strict_types=1);

namespace Modules\School\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\System\Models\Permission;
use Modules\Core\System\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Seeder untuk mendaftarkan izin (permissions) dan peran (roles) standar modul School.
 */
class SchoolRoleSeeder extends Seeder
{
    /**
     * Jalankan database seeding untuk permissions dan roles sekolah.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Akademik
            'view academic',
            'manage academic',
            // Kesiswaan
            'view students',
            'manage students',
            // Kepegawaian (GTK)
            'view staff',
            'manage staff',
            // Disiplin & Poin Kredit
            'view discipline',
            'manage discipline',
            // Bimbingan Konseling (BK)
            'view counseling',
            'manage counseling',
            // Presensi
            'view attendance',
            'manage attendance',
            // Asesmen & Rapor
            'view grades',
            'manage grades',
            // Persuratan & Disposisi TU
            'view letters',
            'manage letters',
            // PPDB
            'view ppdb',
            'manage ppdb',
            // Sarana & Prasarana
            'view facilities',
            'manage facilities',
            // E-Voting & OSIS
            'view voting',
            'manage voting',
            // Integrasi KataMPI
            'view katampi',
            'manage katampi',
            // WhatsApp Gateway
            'view whatsapp',
            'manage whatsapp',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        // 1. Pimpinan Institusi (Akses Penuh Seluruh Domain Sekolah)
        $kepalaSekolah = Role::findOrCreate('kepala-sekolah', 'web');
        $kepalaSekolah->syncPermissions($permissions);

        $adminSekolah = Role::findOrCreate('admin-sekolah', 'web');
        $adminSekolah->syncPermissions($permissions);

        // 2. Administrator Spesialis
        $adminKurikulum = Role::findOrCreate('admin-kurikulum', 'web');
        $adminKurikulum->syncPermissions([
            'view academic', 'manage academic',
            'view students',
            'view staff',
            'view attendance',
            'view grades', 'manage grades',
            'view katampi', 'manage katampi',
        ]);

        $adminKesiswaan = Role::findOrCreate('admin-kesiswaan', 'web');
        $adminKesiswaan->syncPermissions([
            'view students', 'manage students',
            'view discipline', 'manage discipline',
            'view attendance', 'manage attendance',
            'view voting', 'manage voting',
            'view ppdb', 'manage ppdb',
            'view whatsapp',
        ]);

        $adminBK = Role::findOrCreate('admin-bk', 'web');
        $adminBK->syncPermissions([
            'view students',
            'view discipline', 'manage discipline',
            'view counseling', 'manage counseling',
            'view attendance',
        ]);

        $adminSarpras = Role::findOrCreate('admin-sarpras', 'web');
        $adminSarpras->syncPermissions([
            'view facilities', 'manage facilities',
        ]);

        $adminTU = Role::findOrCreate('admin-tu', 'web');
        $adminTU->syncPermissions([
            'view students',
            'view staff',
            'view letters', 'manage letters',
            'view ppdb', 'manage ppdb',
            'view facilities',
        ]);

        // 3. Pendidik & Wali Kelas
        $guru = Role::findOrCreate('guru', 'web');
        $guru->syncPermissions([
            'view academic',
            'view students',
            'view attendance', 'manage attendance',
            'view grades', 'manage grades',
            'view katampi',
        ]);

        $waliKelas = Role::findOrCreate('wali-kelas', 'web');
        $waliKelas->syncPermissions([
            'view academic',
            'view students', 'manage students',
            'view discipline',
            'view attendance', 'manage attendance',
            'view grades', 'manage grades',
            'view letters',
        ]);

        // 4. Peserta Didik & Komunitas
        $siswa = Role::findOrCreate('siswa', 'web');
        $siswa->syncPermissions([
            'view academic',
            'view attendance',
            'view grades',
            'view voting',
        ]);

        $orangTua = Role::findOrCreate('orang-tua', 'web');
        $orangTua->syncPermissions([
            'view academic',
            'view attendance',
            'view grades',
            'view discipline',
        ]);

        $adminOsis = Role::findOrCreate('admin-osis', 'web');
        $adminOsis->syncPermissions([
            'view students',
            'view voting', 'manage voting',
        ]);
    }
}
