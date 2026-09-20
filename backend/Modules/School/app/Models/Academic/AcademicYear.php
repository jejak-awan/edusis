<?php

declare(strict_types=1);

namespace Modules\School\Models\Academic;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\School\Models\Institution\School;

/**
 * @property int $id
 * @property int $school_id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $start_date
 * @property \Illuminate\Support\Carbon|null $end_date
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read School $school
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Semester> $semesters
 * @property-read Semester|null $activeSemester
 * @property-read \Illuminate\Database\Eloquent\Collection<int, StudyGroup> $studyGroups
 */
class AcademicYear extends Model
{
    /** @use HasFactory<\Modules\School\Database\Factories\AcademicYearFactory> */
    use HasFactory;

    protected $table = 'sch_acad_years';

    protected $fillable = [
        'school_id',
        'name',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * @return BelongsTo<School, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    /**
     * @return HasMany<Semester, $this>
     */
    public function semesters(): HasMany
    {
        return $this->hasMany(Semester::class, 'academic_year_id');
    }

    /**
     * @return HasOne<Semester, $this>
     */
    public function activeSemester(): HasOne
    {
        return $this->hasOne(Semester::class, 'academic_year_id')->where('is_active', true);
    }

    /**
     * @return HasMany<StudyGroup, $this>
     */
    public function studyGroups(): HasMany
    {
        return $this->hasMany(StudyGroup::class, 'academic_year_id');
    }
}
