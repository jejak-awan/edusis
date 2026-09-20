<?php

declare(strict_types=1);

namespace Modules\School\Models\Counseling;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\School\Models\Academic\AcademicYear;
use Modules\School\Models\Academic\Semester;
use Modules\School\Models\Discipline\Violation;
use Modules\School\Models\HR\Staff;
use Modules\School\Models\Institution\School;
use Modules\School\Models\Student\Student;

class CounselingCase extends Model
{
    use HasFactory;

    protected $table = 'sch_csl_cases';

    protected $fillable = [
        'school_id',
        'student_id',
        'counselor_id',
        'academic_year_id',
        'semester_id',
        'violation_id',
        'case_number',
        'date',
        'category',
        'confidentiality',
        'problem_description',
        'counseling_steps',
        'solution_plan',
        'follow_up_notes',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function counselor(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'counselor_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class, 'semester_id');
    }

    public function violation(): BelongsTo
    {
        return $this->belongsTo(Violation::class, 'violation_id');
    }
}
