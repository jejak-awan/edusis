<?php

declare(strict_types=1);

namespace Modules\School\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\School\Models\HR\Staff;
use Modules\School\Models\Institution\School;

/**
 * Factory untuk entitas Tenaga Pendidik dan Kependidikan (GTK).
 */
class StaffFactory extends Factory
{
    protected $model = Staff::class;

    public function definition(): array
    {
        $gender = $this->faker->randomElement(['L', 'P']);

        return [
            'school_id' => School::factory(),
            'nip' => $this->faker->unique()->numerify('##################'),
            'nuptk' => $this->faker->unique()->numerify('################'),
            'nik' => $this->faker->unique()->numerify('################'),
            'full_name' => $gender === 'L' ? $this->faker->name('male') : $this->faker->name('female'),
            'title_prefix' => $this->faker->randomElement([null, 'Drs.', 'Dra.']),
            'title_suffix' => $this->faker->randomElement(['S.Pd.', 'M.Pd.', 'S.Kom.', 'M.Kom.']),
            'gender' => $gender,
            'place_of_birth' => $this->faker->city(),
            'date_of_birth' => $this->faker->dateTimeBetween('-55 years', '-25 years')->format('Y-m-d'),
            'religion' => 'Islam',
            'employment_status' => $this->faker->randomElement(['PNS', 'PPPK', 'GTY', 'Honorer']),
            'role_title' => $this->faker->randomElement(['Guru Mata Pelajaran', 'Wali Kelas', 'Guru BK', 'Tenaga Administrasi']),
            'last_education' => $this->faker->randomElement(['S1', 'S2']),
            'major' => 'Pendidikan',
            'certification_status' => true,
            'phone' => '08'.$this->faker->numerify('##########'),
            'email' => $this->faker->unique()->safeEmail(),
            'address' => $this->faker->address(),
            'is_active' => true,
        ];
    }
}
