<?php

declare(strict_types=1);

namespace Modules\School\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\School\Models\Institution\School;

/**
 * Factory untuk entitas Institusi / Sekolah.
 *
 * @extends Factory<School>
 */
class SchoolFactory extends Factory
{
    protected $model = School::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company().' School',
            'type' => $this->faker->randomElement(['SMK', 'SMA', 'SMP', 'SD']),
            'npsn' => $this->faker->unique()->numerify('########'),
            'accreditation' => $this->faker->randomElement(['A', 'B', 'C']),
            'curriculum' => 'Kurikulum Merdeka',
            'address' => $this->faker->streetAddress(),
            'desa_kelurahan' => $this->faker->citySuffix(),
            'kecamatan' => $this->faker->city(),
            'kabupaten_kota' => $this->faker->city(),
            'provinsi' => $this->faker->randomElement(['Jawa Barat', 'DKI Jakarta', 'Jawa Tengah', 'Jawa Timur', 'Banten']),
            'postal_code' => $this->faker->postcode(),
            'phone' => $this->faker->phoneNumber(),
            'email' => $this->faker->unique()->safeEmail(),
            'website' => $this->faker->url(),
            'principal_name' => $this->faker->name(),
            'principal_nip' => $this->faker->numerify('##################'),
            'is_active' => true,
        ];
    }
}
