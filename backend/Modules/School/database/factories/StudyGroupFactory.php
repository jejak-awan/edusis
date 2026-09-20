<?php

declare(strict_types=1);

namespace Modules\School\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\School\Models\Academic\AcademicYear;
use Modules\School\Models\Academic\Department;
use Modules\School\Models\Institution\School;

/**
 * Factory untuk entitas Rombel / Kelas (Study Group).
 */
class StudyGroupFactory extends Factory
{
    protected $model = \Modules\School\Models\Academic\StudyGroup::class;

    public function definition(): array
    {
        $grade = $this->faker->numberBetween(10, 12);
        $suffix = $this->faker->randomElement(['A', 'B', 'C', 'D']);

        return [
            'school_id'       => School::factory(),
            'academic_year_id' => AcademicYear::factory(),
            'department_id'   => null,
            'name'            => "Kelas {$grade} {$suffix}",
            'grade_level'     => $grade,
            'capacity'        => $this->faker->numberBetween(30, 36),
            'is_active'       => true,
        ];
    }
}
