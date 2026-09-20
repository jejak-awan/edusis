<?php

declare(strict_types=1);

namespace Modules\School\Models\Institution;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\School\Models\Academic\StudyGroup;
use Modules\School\Models\Student\Student;

class SchoolLevel extends Model
{
    use HasFactory;

    protected $table = 'sch_ins_levels';

    protected $fillable = [
        'school_id',
        'name',
        'grade_level',
        'is_active',
    ];

    protected $casts = [
        'grade_level' => 'integer',
        'is_active' => 'boolean',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    public function studyGroups(): HasMany
    {
        return $this->hasMany(StudyGroup::class, 'school_level_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'school_level_id');
    }
}
