<?php

declare(strict_types=1);

namespace Modules\School\Models\Academic;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\School\Models\HR\Staff;
use Modules\School\Models\Institution\School;

/**
 * @property int $id
 * @property int $school_id
 * @property int $academic_year_id
 * @property int|null $coordinator_id
 * @property string $title
 * @property string $theme
 * @property string $phase
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read School $school
 * @property-read AcademicYear $academicYear
 * @property-read Staff|null $coordinator
 * @property-read \Illuminate\Database\Eloquent\Collection<int, P5Assessment> $assessments
 */
class P5Project extends Model
{
    protected $table = 'sch_acad_p5_projects';

    protected $fillable = [
        'school_id',
        'academic_year_id',
        'coordinator_id',
        'title',
        'theme',
        'phase',
        'description',
    ];

    /**
     * @return BelongsTo<School, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    /**
     * @return BelongsTo<AcademicYear, $this>
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    /**
     * @return BelongsTo<Staff, $this>
     */
    public function coordinator(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'coordinator_id');
    }

    /**
     * @return HasMany<P5Assessment, $this>
     */
    public function assessments(): HasMany
    {
        return $this->hasMany(P5Assessment::class, 'project_id');
    }
}
