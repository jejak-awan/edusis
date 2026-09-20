<?php

declare(strict_types=1);

namespace Modules\School\Models\Institution;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\School\Models\Academic\AcademicYear;
use Modules\School\Models\Academic\Department;
use Modules\School\Models\Academic\StudyGroup;
use Modules\School\Models\Academic\Subject;
use Modules\School\Models\Student\Student;

/**
 * @property int $id
 * @property string $name
 * @property string $type
 * @property string $npsn
 * @property string|null $nss
 * @property string|null $accreditation
 * @property string|null $curriculum
 * @property string|null $address
 * @property string|null $principal_name
 * @property string|null $principal_nip
 * @property bool $is_active
 */
class School extends Model
{
    use HasFactory;

    protected $table = 'sch_ins_schools';

    protected $fillable = [
        'name',
        'type',
        'npsn',
        'nss',
        'accreditation',
        'curriculum',
        'address',
        'rt',
        'rw',
        'dusun',
        'desa_kelurahan',
        'kecamatan',
        'kabupaten_kota',
        'provinsi',
        'postal_code',
        'latitude',
        'longitude',
        'phone',
        'email',
        'website',
        'principal_name',
        'principal_nip',
        'vision',
        'mission',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    public function levels(): HasMany
    {
        return $this->hasMany(SchoolLevel::class, 'school_id');
    }

    public function academicYears(): HasMany
    {
        return $this->hasMany(AcademicYear::class, 'school_id');
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class, 'school_id');
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class, 'school_id');
    }

    public function studyGroups(): HasMany
    {
        return $this->hasMany(StudyGroup::class, 'school_id');
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'school_id');
    }
}
