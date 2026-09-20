<?php

declare(strict_types=1);

namespace Modules\School\Models\Academic;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\School\Models\Institution\School;
use Modules\School\Models\Institution\SchoolLevel;
use Modules\School\Models\Student\Student;

class StudyGroup extends Model
{
    use HasFactory;

    protected $table = 'sch_acad_study_groups';

    protected $fillable = [
        'school_id',
        'school_level_id',
        'department_id',
        'academic_year_id',
        'name',
        'homeroom_teacher_id',
        'capacity',
        'is_active',
    ];

    protected $casts = [
        'capacity' => 'integer',
        'is_active' => 'boolean',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(SchoolLevel::class, 'school_level_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function groupMembers(): HasMany
    {
        return $this->hasMany(StudyGroupMember::class, 'study_group_id');
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'sch_acad_study_group_members', 'study_group_id', 'student_id')
            ->withPivot('status')
            ->withTimestamps();
    }
}
