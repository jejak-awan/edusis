<?php

declare(strict_types=1);

namespace Modules\School\Models\Academic;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\School\Models\HR\Staff;
use Modules\School\Models\Institution\School;

/**
 * @property int $id
 * @property int $school_id
 * @property int $academic_year_id
 * @property int $semester_id
 * @property int $study_group_id
 * @property int $subject_id
 * @property int $teacher_id
 * @property int|null $schedule_id
 * @property \Illuminate\Support\Carbon|null $date
 * @property string $teaching_hour
 * @property string $topic
 * @property string|null $competency_description
 * @property string|null $learning_activity
 * @property int $present_count
 * @property int $sick_count
 * @property int $permit_count
 * @property int $absent_count
 * @property string|null $notes_reflection
 * @property string|null $evidence_photo_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read School $school
 * @property-read AcademicYear $academicYear
 * @property-read Semester $semester
 * @property-read StudyGroup $studyGroup
 * @property-read Subject $subject
 * @property-read Staff $teacher
 * @property-read Schedule|null $schedule
 */
class TeachingJournal extends Model
{
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
     * @return BelongsTo<Schedule, $this>
     */
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class, 'schedule_id');
    }
}
