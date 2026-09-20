<?php

declare(strict_types=1);

namespace Modules\School\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\School\Models\Academic\AcademicYear;
use Modules\School\Models\Institution\School;
use Modules\School\Models\Voting\VoteEvent;

/**
 * Factory untuk entitas E-Voting Event (Pemilihan Digital).
 */
class VoteEventFactory extends Factory
{
    protected $model = VoteEvent::class;

    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('now', '+7 days');
        $endDate = $this->faker->dateTimeBetween('+8 days', '+14 days');

        return [
            'school_id' => School::factory(),
            'academic_year_id' => AcademicYear::factory(),
            'title' => 'Pemilihan '.$this->faker->randomElement(['Ketua OSIS', 'Ketua MPK', 'Ekstrakurikuler Favorit']),
            'type' => $this->faker->randomElement(['osis', 'mpk', 'ekskul', 'polling']),
            'description' => $this->faker->optional()->sentence(),
            'target_audience' => $this->faker->randomElement(['all_students', 'specific_classes']),
            'voting_mode' => $this->faker->randomElement(['token_kiosk', 'online_login']),
            'start_date' => $startDate->format('Y-m-d H:i:s'),
            'end_date' => $endDate->format('Y-m-d H:i:s'),
            'status' => 'draft',
            'allow_abstain' => $this->faker->boolean(20),
        ];
    }

    /** State untuk event yang sedang aktif. */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'start_date' => now()->subHour()->format('Y-m-d H:i:s'),
            'end_date' => now()->addDays(3)->format('Y-m-d H:i:s'),
        ]);
    }
}
