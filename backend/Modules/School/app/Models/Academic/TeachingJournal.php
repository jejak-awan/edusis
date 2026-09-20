<?php

declare(strict_types=1);

namespace Modules\School\Models\Academic;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\School\Models\HR\Staff;
use Modules\School\Models\Institution\School;

class TeachingJournal extends Model
{
    use HasFactory;

    protected $table = 'sch_acad_teaching_journals';

    protected $fillable = [
        'school_id',
        'academic_year_id',
        'semester_id',
        'study_group_id',
        'subject_id',
        'teacher_id',
        'schedule_id',
        'date',
        'teaching_hour',
        'topic',
        'competency_description',
        'learning_activity',
        'present_count',
        'sick_count',
        'permit_count',
        'absent_count',
        'notes_reflection',
        'evidence_photo_path',
    ];

    protected $casts = [
        'date' => 'date',
        'present_count' => 'integer',
        'sick_count' => 'integer',
        'permit_count' => 'integer',
        'absent_count' => 'integer',
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

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class, 'schedule_id');
    }
}
