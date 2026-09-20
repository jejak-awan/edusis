<?php

declare(strict_types=1);

namespace Modules\School\Models\Academic;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\School\Models\Institution\School;
use Modules\School\Models\Student\Student;

/**
 * @property int $id
 * @property int $school_id
 * @property int $academic_year_id
 * @property int $semester_id
 * @property int $study_group_id
 * @property int $student_id
 * @property int $subject_id
 * @property string|float $final_score
 * @property string $letter_grade
 * @property string|null $achieved_competency
 * @property string|null $improved_competency
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read School $school
 * @property-read AcademicYear $academicYear
 * @property-read Semester $semester
 * @property-read StudyGroup $studyGroup
 * @property-read Student $student
 * @property-read Subject $subject
 */
class ReportCard extends Model
{
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
     * @return BelongsTo<Student, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * @return BelongsTo<Subject, $this>
     */
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }
}
