<?php

declare(strict_types=1);

namespace Modules\School\Models\Student;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $student_id
 * @property string|null $father_name
 * @property string|null $father_nik
 * @property int|null $father_birth_year
 * @property string|null $father_education
 * @property string|null $father_occupation
 * @property string|null $father_income
 * @property string|null $father_phone
 * @property string|null $mother_name
 * @property string|null $mother_nik
 * @property int|null $mother_birth_year
 * @property string|null $mother_education
 * @property string|null $mother_occupation
 * @property string|null $mother_income
 * @property string|null $mother_phone
 * @property string|null $guardian_name
 * @property string|null $guardian_nik
 * @property int|null $guardian_birth_year
 * @property string|null $guardian_education
 * @property string|null $guardian_occupation
 * @property string|null $guardian_income
 * @property string|null $guardian_phone
 * @property string|null $guardian_relation
 * @property string|null $economic_status
 * @property bool $is_pkh
 * @property bool $is_kip
 * @property string|null $kip_number
 * @property string|null $family_card_number
 * @property string|null $emergency_phone
 * @property string|null $emergency_contact_name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Student|null $student
 */
class StudentParent extends Model
{
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

    /**
     * @return BelongsTo<Student, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
