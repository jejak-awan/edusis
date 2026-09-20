<?php

declare(strict_types=1);

namespace Modules\School\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\School\Models\Academic\Department;
use Modules\School\Models\Institution\School;

/**
 * Factory untuk entitas Kompetensi Keahlian / Jurusan (Department).
 */
class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    public function definition(): array
    {
        $departments = [
            ['code' => 'RPL', 'name' => 'Rekayasa Perangkat Lunak'],
            ['code' => 'TKJ', 'name' => 'Teknik Komputer & Jaringan'],
            ['code' => 'MM',  'name' => 'Multimedia'],
            ['code' => 'AKL', 'name' => 'Akuntansi & Keuangan Lembaga'],
            ['code' => 'OTKP', 'name' => 'Otomatisasi & Tata Kelola Perkantoran'],
            ['code' => 'BDP', 'name' => 'Bisnis Daring & Pemasaran'],
        ];
        $dept = $this->faker->randomElement($departments);

        return [
            'school_id' => School::factory(),
            'code' => $dept['code'],
            'name' => $dept['name'],
            'description' => $this->faker->optional()->sentence(),
            'is_active' => true,
        ];
    }
}
