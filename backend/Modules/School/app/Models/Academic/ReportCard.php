<?php

declare(strict_types=1);

namespace Modules\School\Models\Academic;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\School\Models\Institution\School;
use Modules\School\Models\Student\Student;

class ReportCard extends Model
{
    use HasFactory;

    protected $table = 'sch_acad_report_cards';

    protected $fillable = [
        'school_id',
        'academic_year_id',
        'semester_id',
        'study_group_id',
        'student_id',
        'subject_id',
        'final_score',
        'letter_grade',
        'achieved_competency',
        'improved_competency',
    ];

    protected $casts = [
        'final_score' => 'decimal:2',
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

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }
}
