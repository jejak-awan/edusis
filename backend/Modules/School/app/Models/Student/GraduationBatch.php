<?php

declare(strict_types=1);

namespace Modules\School\Models\Student;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\School\Models\Academic\AcademicYear;
use Modules\School\Models\Institution\School;
use Modules\School\Traits\ScopedBySchool;

/**
 * @property int $id
 * @property int $school_id
 * @property int|null $academic_year_id
 * @property string $batch_year
 * @property string $batch_name
 * @property \Illuminate\Support\Carbon $graduation_date
 * @property string|null $decree_number
 * @property int $total_students
 * @property string $status
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read School|null $school
 * @property-read AcademicYear|null $academicYear
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Alumni> $alumnis
 */
class GraduationBatch extends Model
{
    use HasFactory, ScopedBySchool;

    protected $table = 'sch_graduation_batches';

    protected $fillable = [
        'school_id',
        'academic_year_id',
        'batch_year',
        'batch_name',
        'graduation_date',
        'decree_number',
        'total_students',
        'status',
        'notes',
    ];

    protected $casts = [
        'graduation_date' => 'date',
        'total_students' => 'integer',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function alumnis(): HasMany
    {
        return $this->hasMany(Alumni::class, 'graduation_batch_id');
    }
}
