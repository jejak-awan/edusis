<?php

declare(strict_types=1);

namespace Modules\School\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\School\Models\Institution\School;
use Modules\School\Models\Student\Student;

/**
 * Factory untuk entitas Peserta Didik (Buku Induk Siswa Dapodik).
 *
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        $gender = $this->faker->randomElement(['L', 'P']);
        $religions = ['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha', 'Konghucu'];

        return [
            'school_id' => School::factory(),
            'nisn' => $this->faker->unique()->numerify('##########'),
            'nis' => $this->faker->numerify('####'),
            'nik' => $this->faker->numerify('################'),
            'full_name' => $gender === 'L' ? $this->faker->name('male') : $this->faker->name('female'),
            'place_of_birth' => $this->faker->city(),
            'date_of_birth' => $this->faker->dateTimeBetween('-18 years', '-15 years')->format('Y-m-d'),
            'gender' => $gender,
            'religion' => $this->faker->randomElement($religions),
            'address' => $this->faker->address(),
            'rt' => $this->faker->numerify('0##'),
            'rw' => $this->faker->numerify('0##'),
            'desa_kelurahan' => $this->faker->citySuffix(),
            'kecamatan' => $this->faker->city(),
            'kabupaten_kota' => $this->faker->city(),
            'provinsi' => $this->faker->randomElement(['Jawa Barat', 'DKI Jakarta', 'Jawa Tengah', 'Jawa Timur', 'Banten']),
            'postal_code' => $this->faker->postcode(),
            'phone' => '08'.$this->faker->numerify('##########'),
            'email' => $this->faker->unique()->safeEmail(),
            'entry_year' => 2026,
            'status' => 'active',
        ];
    }

    public function active(): static
    {
        return $this->state(fn (): array => ['status' => 'active']);
    }

    public function graduated(): static
    {
        return $this->state(fn (): array => ['status' => 'graduated']);
    }
}
