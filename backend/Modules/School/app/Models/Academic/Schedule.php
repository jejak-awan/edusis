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
 * @property int $teacher_id
 * @property int $day_of_week
 * @property string $start_time
 * @property string $end_time
 * @property string|null $room_name
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $day_name
 * @property-read School $school
 * @property-read AcademicYear $academicYear
 * @property-read Semester $semester
 * @property-read StudyGroup $studyGroup
 * @property-read Subject $subject
 * @property-read Staff $teacher
 * @property-read \Illuminate\Database\Eloquent\Collection<int, TeachingJournal> $journals
 */
class Schedule extends Model
{
    protected $table = 'sch_acad_schedules';

    protected $fillable = [
        'school_id',
        'academic_year_id',
        'semester_id',
        'study_group_id',
        'subject_id',
        'teacher_id',
        'day_of_week',
        'start_time',
        'end_time',
        'room_name',
        'is_active',
    ];

    protected $casts = [
        'day_of_week' => 'integer',
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
     * @return HasMany<TeachingJournal, $this>
     */
    public function journals(): HasMany
    {
        return $this->hasMany(TeachingJournal::class, 'schedule_id');
    }

    /**
     * Nama hari dalam Bahasa Indonesia
     */
    public function getDayNameAttribute(): string
    {
        $days = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
        ];

        return $days[$this->day_of_week] ?? 'Senin';
    }
}
