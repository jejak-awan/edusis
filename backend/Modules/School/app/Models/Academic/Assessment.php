<?php

declare(strict_types=1);

namespace Modules\School\Models\Academic;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\School\Models\HR\Staff;
use Modules\School\Models\Institution\School;

class Assessment extends Model
{
    use HasFactory;

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

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    public function studyGroup(): BelongsTo
    {
        return $this->belongsTo(StudyGroup::class, 'study_group_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'teacher_id');
    }

    public function learningObjective(): BelongsTo
    {
        return $this->belongsTo(LearningObjective::class, 'learning_objective_id');
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class, 'assessment_id');
    }
}
