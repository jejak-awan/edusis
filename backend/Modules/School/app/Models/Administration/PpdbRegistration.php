<?php

declare(strict_types=1);

namespace Modules\School\Models\Administration;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\School\Models\Academic\AcademicYear;
use Modules\School\Models\Academic\Department;
use Modules\School\Models\HR\Staff;
use Modules\School\Models\Institution\School;

/**
 * @property int $id
 * @property int $school_id
 * @property int $academic_year_id
 * @property int $department_id
 * @property int|null $second_choice_department_id
 * @property string $registration_number
 * @property string $track
 * @property string $full_name
 * @property string $nisn
 * @property string|null $nik
 * @property string $gender
 * @property string|null $place_of_birth
 * @property \Illuminate\Support\Carbon|null $date_of_birth
 * @property string|null $religion
 * @property string|null $phone
 * @property string|null $email
 * @property string|null $address
 * @property string|null $parent_name
 * @property string|null $parent_phone
 * @property string|null $parent_job
 * @property string|null $parent_income
 * @property string|null $previous_school
 * @property float|null $average_report_score
 * @property string $status
 * @property array<string, mixed>|null $document_paths
 * @property int|null $verified_by
 * @property \Illuminate\Support\Carbon|null $verified_at
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Department|null $department
 * @property-read Department|null $secondChoiceDepartment
 */
class PpdbRegistration extends Model
{
    protected $table = 'sch_adm_ppdb_registrations';

    protected $fillable = [
        'school_id',
        'academic_year_id',
        'department_id',
        'second_choice_department_id',
        'registration_number',
        'track',
        'full_name',
        'nisn',
        'nik',
        'gender',
        'place_of_birth',
        'date_of_birth',
        'religion',
        'phone',
        'email',
        'address',
        'parent_name',
        'parent_phone',
        'parent_job',
        'parent_income',
        'previous_school',
        'average_report_score',
        'status',
        'document_paths',
        'verified_by',
        'verified_at',
        'notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'verified_at' => 'datetime',
        'document_paths' => 'array',
        'average_report_score' => 'decimal:2',
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
     * @return BelongsTo<Department, $this>
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * @return BelongsTo<Department, $this>
     */
    public function secondChoiceDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'second_choice_department_id');
    }

    /**
     * @return BelongsTo<Staff, $this>
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'verified_by');
    }
}
