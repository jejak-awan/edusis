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

class PpdbRegistration extends Model
{
    use HasFactory;

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

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function secondChoiceDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'second_choice_department_id');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'verified_by');
    }
}
