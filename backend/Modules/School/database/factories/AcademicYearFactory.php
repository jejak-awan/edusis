<?php

declare(strict_types=1);

namespace Modules\School\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\School\Models\Academic\AcademicYear;
use Modules\School\Models\Institution\School;

/**
 * Factory untuk entitas Tahun Ajaran Akademik.
 *
 * @extends Factory<AcademicYear>
 */
class AcademicYearFactory extends Factory
{
    protected $model = AcademicYear::class;

    public function definition(): array
    {
        $startYear = $this->faker->numberBetween(2024, 2028);
        $endYear = $startYear + 1;

        return [
            'school_id' => School::factory(),
            'name' => "{$startYear}/{$endYear}",
            'start_date' => "{$startYear}-07-15",
            'end_date' => "{$endYear}-06-30",
            'is_active' => true,
        ];
    }
}
