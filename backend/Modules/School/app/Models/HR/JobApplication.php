<?php

declare(strict_types=1);

namespace Modules\School\Models\HR;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\School\Models\Student\Alumni;
use Modules\School\Models\Student\Student;

/**
 * @property int $id
 * @property int $vacancy_id
 * @property int|null $student_id
 * @property int|null $alumni_id
 * @property string $applicant_name
 * @property string $applicant_email
 * @property string $applicant_phone
 * @property string|null $resume_url
 * @property string|null $portfolio_url
 * @property string|null $cover_letter
 * @property string $status
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read JobVacancy|null $vacancy
 * @property-read Student|null $student
 * @property-read Alumni|null $alumni
 */
class JobApplication extends Model
{
    use HasFactory;

    protected $table = 'sch_career_applications';

    protected $fillable = [
        'vacancy_id',
        'student_id',
        'alumni_id',
        'applicant_name',
        'applicant_email',
        'applicant_phone',
        'resume_url',
        'portfolio_url',
        'cover_letter',
        'status',
        'notes',
    ];

    public function vacancy(): BelongsTo
    {
        return $this->belongsTo(JobVacancy::class, 'vacancy_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function alumni(): BelongsTo
    {
        return $this->belongsTo(Alumni::class, 'alumni_id');
    }
}
