<?php

declare(strict_types=1);

namespace Modules\School\Models\Student;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Modules\Core\System\Models\User;
use Modules\School\Models\Academic\AcademicYear;
use Modules\School\Models\Academic\Semester;

/**
 * @property int $id
 * @property int $student_id
 * @property int $category_id
 * @property int|null $academic_year_id
 * @property int|null $semester_id
 * @property int $points
 * @property string $description
 * @property \Illuminate\Support\Carbon $date
 * @property int|string|null $reporter_id
 * @property string $status
 * @property string|null $admin_notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Student|null $student
 * @property-read PointCategory|null $category
 * @property-read AcademicYear|null $academicYear
 * @property-read Semester|null $semester
 * @property-read User|null $reporter
 * @property-read Violation|null $violation
 * @property-read Achievement|null $achievement
 */
class CreditPoint extends Model
{
    use HasFactory;

    protected $table = 'sch_std_credit_points';

    protected $fillable = [
        'student_id',
        'category_id',
        'academic_year_id',
        'semester_id',
        'points',
        'description',
        'date',
        'reporter_id',
        'status',
        'admin_notes',
    ];

    protected $casts = [
        'points' => 'integer',
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
     * @return BelongsTo<PointCategory, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(PointCategory::class, 'category_id');
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
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    /**
     * @return HasOne<Violation, $this>
     */
    public function violation(): HasOne
    {
        return $this->hasOne(Violation::class, 'credit_point_id');
    }

    /**
     * @return HasOne<Achievement, $this>
     */
    public function achievement(): HasOne
    {
        return $this->hasOne(Achievement::class, 'credit_point_id');
    }
}
