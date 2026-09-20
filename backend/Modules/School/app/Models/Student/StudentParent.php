<?php

declare(strict_types=1);

namespace Modules\School\Models\Student;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentParent extends Model
{
    use HasFactory;

    protected $table = 'sch_std_parents';

    protected $fillable = [
        'student_id',
        'father_name',
        'father_nik',
        'father_birth_year',
        'father_education',
        'father_occupation',
        'father_income',
        'father_phone',
        'mother_name',
        'mother_nik',
        'mother_birth_year',
        'mother_education',
        'mother_occupation',
        'mother_income',
        'mother_phone',
        'guardian_name',
        'guardian_nik',
        'guardian_birth_year',
        'guardian_education',
        'guardian_occupation',
        'guardian_income',
        'guardian_phone',
        'guardian_relation',
        'economic_status',
        'is_pkh',
        'is_kip',
        'kip_number',
        'family_card_number',
        'emergency_phone',
        'emergency_contact_name',
    ];

    protected $casts = [
        'father_birth_year' => 'integer',
        'mother_birth_year' => 'integer',
        'guardian_birth_year' => 'integer',
        'is_pkh' => 'boolean',
        'is_kip' => 'boolean',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
