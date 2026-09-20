<?php

declare(strict_types=1);

namespace Modules\School\Models\Academic;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\School\Models\HR\Staff;
use Modules\School\Models\Institution\School;
use Modules\School\Models\Institution\SchoolLevel;
use Modules\School\Models\Student\Student;

/**
 * @property int $id
 * @property int $school_id
 * @property int|null $school_level_id
 * @property int|null $department_id
 * @property int $academic_year_id
 * @property string $name
 * @property int|null $homeroom_teacher_id
 * @property int $capacity
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Student> $students
 * @property-read Department|null $department
 * @property-read SchoolLevel|null $level
 * @property-read School|null $school
 * @property-read AcademicYear|null $academicYear
 * @property-read Staff|null $homeroomTeacher
 */
class StudyGroup extends Model
{
    /** @use HasFactory<\Modules\School\Database\Factories\StudyGroupFactory> */
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

    /**
     * @return BelongsTo<School, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    /**
     * @return BelongsTo<SchoolLevel, $this>
     */
    public function level(): BelongsTo
    {
        return $this->belongsTo(SchoolLevel::class, 'school_level_id');
    }

    /**
     * @return BelongsTo<Department, $this>
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * @return BelongsTo<AcademicYear, $this>
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    /**
     * @return HasMany<StudyGroupMember, $this>
     */
    public function groupMembers(): HasMany
    {
        return $this->hasMany(StudyGroupMember::class, 'study_group_id');
    }

    /**
     * @return BelongsToMany<Student, $this>
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'sch_acad_study_group_members', 'study_group_id', 'student_id')
            ->withPivot('status')
            ->withTimestamps();
    }

    /**
     * @return BelongsTo<Staff, $this>
     */
    public function homeroomTeacher(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'homeroom_teacher_id');
    }
}
