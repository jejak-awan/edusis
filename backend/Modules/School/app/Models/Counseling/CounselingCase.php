<?php

declare(strict_types=1);

namespace Modules\School\Models\Counseling;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\School\Models\Academic\AcademicYear;
use Modules\School\Models\Academic\Semester;
use Modules\School\Models\HR\Staff;
use Modules\School\Models\Institution\School;
use Modules\School\Models\Student\Student;
use Modules\School\Models\Student\Violation;

/**
 * @property int $id
 * @property int $school_id
 * @property int $student_id
 * @property int $counselor_id
 * @property int $academic_year_id
 * @property int $semester_id
 * @property int|null $violation_id
 * @property string $case_number
 * @property \Illuminate\Support\Carbon|null $date
 * @property string $category
 * @property string $confidentiality
 * @property string $problem_description
 * @property string|null $counseling_steps
 * @property string|null $solution_plan
 * @property string|null $follow_up_notes
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read School $school
 * @property-read Student $student
 * @property-read Staff $counselor
 * @property-read AcademicYear $academicYear
 * @property-read Semester $semester
 * @property-read Violation|null $violation
 */
class CounselingCase extends Model
{
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

    /**
     * @return BelongsTo<School, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    /**
     * @return BelongsTo<Student, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * @return BelongsTo<Staff, $this>
     */
    public function counselor(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'counselor_id');
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
     * @return BelongsTo<Violation, $this>
     */
    public function violation(): BelongsTo
    {
        return $this->belongsTo(Violation::class, 'violation_id');
    }
}
