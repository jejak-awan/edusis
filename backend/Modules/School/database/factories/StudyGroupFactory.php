<?php

declare(strict_types=1);

namespace Modules\School\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\School\Models\Academic\AcademicYear;
use Modules\School\Models\Academic\StudyGroup;
use Modules\School\Models\Institution\School;

/**
 * Factory untuk entitas Rombel / Kelas (Study Group).
 *
 * @extends Factory<StudyGroup>
 */
class StudyGroupFactory extends Factory
{
    protected $model = StudyGroup::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $grade = $this->faker->numberBetween(10, 12);
        $suffix = is_string($this->faker->randomElement(['A', 'B', 'C', 'D'])) ? $this->faker->randomElement(['A', 'B', 'C', 'D']) : 'A';

        return [
            'school_id' => School::factory(),
            'academic_year_id' => AcademicYear::factory(),
            'department_id' => null,
            'name' => "Kelas {$grade} {$suffix}",
            'grade_level' => $grade,
            'capacity' => $this->faker->numberBetween(30, 36),
            'is_active' => true,
        ];
    }
}
