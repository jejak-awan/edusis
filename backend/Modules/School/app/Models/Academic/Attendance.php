<?php

declare(strict_types=1);

namespace Modules\School\Models\Academic;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Core\System\Models\User;
use Modules\School\Models\Student\Student;

/**
 * @property int $id
 * @property int $student_id
 * @property int $study_group_id
 * @property int $academic_year_id
 * @property int $semester_id
 * @property \Illuminate\Support\Carbon|null $date
 * @property string $status
 * @property string|null $notes
 * @property string|null $attachment_path
 * @property int|null $recorded_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Student|null $student
 * @property-read StudyGroup|null $studyGroup
 */
class Attendance extends Model
{
    protected $table = 'sch_acad_attendances';

    protected $fillable = [
        'student_id',
        'study_group_id',
        'academic_year_id',
        'semester_id',
        'date',
        'status',
        'notes',
        'attachment_path',
        'recorded_by',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    /**
     * @return BelongsTo<Student, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * @return BelongsTo<StudyGroup, $this>
     */
    public function studyGroup(): BelongsTo
    {
        return $this->belongsTo(StudyGroup::class, 'study_group_id');
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
     * @return BelongsTo<User, $this>
     */
    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /**
     * Label status kehadiran
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'H' => 'Hadir',
            'I' => 'Izin',
            'S' => 'Sakit',
            'A' => 'Alpa',
            default => 'Hadir',
        };
    }
}
