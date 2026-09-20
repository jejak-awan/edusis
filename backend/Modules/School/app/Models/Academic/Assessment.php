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
 * @property int $semester_id
 * @property int $study_group_id
 * @property int $subject_id
 * @property int|null $teacher_id
 * @property int|null $learning_objective_id
 * @property string $title
 * @property string $type
 * @property int $weight
 * @property int $max_score
 * @property \Illuminate\Support\Carbon|null $date
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Grade> $grades
 * @property-read Subject|null $subject
 * @property-read StudyGroup|null $studyGroup
 * @property-read School|null $school
 * @property-read AcademicYear|null $academicYear
 * @property-read Semester|null $semester
 * @property-read Staff|null $teacher
 * @property-read LearningObjective|null $learningObjective
 */
class Assessment extends Model
{
    protected $table = 'sch_acad_assessments';

    protected $fillable = [
        'school_id',
        'academic_year_id',
        'semester_id',
        'study_group_id',
        'subject_id',
        'teacher_id',
        'learning_objective_id',
        'title',
        'type',
        'weight',
        'max_score',
        'date',
        'description',
    ];

    protected $casts = [
        'weight' => 'integer',
        'max_score' => 'integer',
        'date' => 'date',
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
     * @return BelongsTo<Semester, $this>
     */
    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    /**
     * @return BelongsTo<StudyGroup, $this>
     */
    public function studyGroup(): BelongsTo
    {
        return $this->belongsTo(StudyGroup::class, 'study_group_id');
    }

    /**
     * @return BelongsTo<Subject, $this>
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    /**
     * @return BelongsTo<Staff, $this>
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'teacher_id');
    }

    /**
     * @return BelongsTo<LearningObjective, $this>
     */
    public function learningObjective(): BelongsTo
    {
        return $this->belongsTo(LearningObjective::class, 'learning_objective_id');
    }

    /**
     * @return HasMany<Grade, $this>
     */
    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class, 'assessment_id');
    }
}
