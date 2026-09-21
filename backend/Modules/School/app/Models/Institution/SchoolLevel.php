<?php

declare(strict_types=1);

namespace Modules\School\Models\Institution;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\School\Models\Academic\StudyGroup;
use Modules\School\Models\Student\Student;

/**
 * @property int $id
 * @property int $school_id
 * @property string $name
 * @property int $grade_level
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read School $school
 * @property-read \Illuminate\Database\Eloquent\Collection<int, StudyGroup> $studyGroups
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Student> $students
 */
class SchoolLevel extends Model
{
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

    /**
     * @return BelongsTo<School, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    /**
     * @return HasMany<StudyGroup, $this>
     */
    public function studyGroups(): HasMany
    {
        return $this->hasMany(StudyGroup::class, 'school_level_id');
    }

    /**
     * @return HasMany<Student, $this>
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'school_level_id');
    }
}
