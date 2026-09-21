<?php

declare(strict_types=1);

namespace Modules\School\Models\HR;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\School\Models\Academic\Department;
use Modules\School\Models\Institution\School;
use Modules\School\Traits\ScopedBySchool;

/**
 * @property int $id
 * @property int $school_id
 * @property int|null $department_id
 * @property string $company_name
 * @property string|null $company_logo_url
 * @property string $job_title
 * @property string $job_type
 * @property string $workplace_type
 * @property string|null $location
 * @property string|null $salary_range
 * @property string $description
 * @property string|null $requirements
 * @property string|null $benefits
 * @property \Illuminate\Support\Carbon|null $application_deadline
 * @property string|null $contact_email
 * @property string|null $contact_phone
 * @property string|null $external_url
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read School|null $school
 * @property-read Department|null $department
 * @property-read \Illuminate\Database\Eloquent\Collection<int, JobApplication> $applications
 */
class JobVacancy extends Model
{
    use HasFactory, ScopedBySchool;

    protected $table = 'sch_career_vacancies';

    protected $fillable = [
        'school_id',
        'department_id',
        'company_name',
        'company_logo_url',
        'job_title',
        'job_type',
        'workplace_type',
        'location',
        'salary_range',
        'description',
        'requirements',
        'benefits',
        'application_deadline',
        'contact_email',
        'contact_phone',
        'external_url',
        'status',
    ];

    protected $casts = [
        'application_deadline' => 'date',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function applications(): HasMany
    {
        return $this->hasMany(JobApplication::class, 'vacancy_id');
    }
}
