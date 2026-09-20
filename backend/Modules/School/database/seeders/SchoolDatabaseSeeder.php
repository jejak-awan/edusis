<?php

declare(strict_types=1);

namespace Modules\School\Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Master Database Seeder Orchestrator untuk Modul School.
 * Dijalankan otomatis saat aktivasi ekstensi sesuai kontrak manifest.json.
 */
class SchoolDatabaseSeeder extends Seeder
{
    /**
     * Jalankan database seeds seluruh fase modul School.
     */
    public function run(): void
    {
        $this->call([
            SchoolRoleSeeder::class,
            SchoolPhase1Seeder::class,
            SchoolPhase2Seeder::class,
            SchoolPhase3Seeder::class,
            SchoolPhase4Seeder::class,
            SchoolPhase5Seeder::class,
            SchoolVotingEngineSeeder::class,
        ]);
    }
}
