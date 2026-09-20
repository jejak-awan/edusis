<?php

declare(strict_types=1);

namespace Modules\School\Models\Academic;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\School\Models\Institution\School;

class AcademicYear extends Model
{
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

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    public function semesters(): HasMany
    {
        return $this->hasMany(Semester::class, 'academic_year_id');
    }

    public function activeSemester(): HasOne
    {
        return $this->hasOne(Semester::class, 'academic_year_id')->where('is_active', true);
    }

    public function studyGroups(): HasMany
    {
        return $this->hasMany(StudyGroup::class, 'academic_year_id');
    }
}
