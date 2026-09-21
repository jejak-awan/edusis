<?php

declare(strict_types=1);

namespace Modules\School\Models\Student;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\School\Models\HR\JobApplication;
use Modules\School\Models\Institution\School;
use Modules\School\Traits\ScopedBySchool;

/**
 * @property int $id
 * @property int $school_id
 * @property int $student_id
 * @property int|null $graduation_batch_id
 * @property string $graduation_year
 * @property string|null $skl_number
 * @property string|null $certificate_number
 * @property float|null $average_score
 * @property string $career_status
 * @property string|null $company_or_campus
 * @property string|null $current_position
 * @property string|null $salary_range
 * @property string|null $contact_phone
 * @property string|null $contact_email
 * @property string|null $linkedin_url
 * @property string|null $portfolio_url
 * @property string|null $address
 * @property string|null $notes
 * @property bool $is_public
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read School|null $school
 * @property-read Student|null $student
 * @property-read GraduationBatch|null $batch
 * @property-read \Illuminate\Database\Eloquent\Collection<int, JobApplication> $jobApplications
 */
class Alumni extends Model
{
    use HasFactory, ScopedBySchool;

    protected $table = 'sch_alumnis';

    protected $fillable = [
        'school_id',
        'student_id',
        'graduation_batch_id',
        'graduation_year',
        'skl_number',
        'certificate_number',
        'average_score',
        'career_status',
        'company_or_campus',
        'current_position',
        'salary_range',
        'contact_phone',
        'contact_email',
        'linkedin_url',
        'portfolio_url',
        'address',
        'notes',
        'is_public',
    ];

    protected $casts = [
        'average_score' => 'float',
        'is_public' => 'boolean',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(GraduationBatch::class, 'graduation_batch_id');
    }

    public function jobApplications(): HasMany
    {
        return $this->hasMany(JobApplication::class, 'alumni_id');
    }
}
