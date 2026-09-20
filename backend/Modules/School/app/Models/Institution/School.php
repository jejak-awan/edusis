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
 * @property string|null $rt
 * @property string|null $rw
 * @property string|null $dusun
 * @property string|null $desa_kelurahan
 * @property string|null $kecamatan
 * @property string|null $kabupaten_kota
 * @property string|null $provinsi
 * @property string|null $postal_code
 * @property string|null $latitude
 * @property string|null $longitude
 * @property string|null $phone
 * @property string|null $email
 * @property string|null $website
 * @property string|null $principal_name
 * @property string|null $principal_nip
 * @property string|null $vision
 * @property string|null $mission
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, SchoolLevel> $levels
 * @property-read \Illuminate\Database\Eloquent\Collection<int, AcademicYear> $academicYears
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Department> $departments
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Subject> $subjects
 * @property-read \Illuminate\Database\Eloquent\Collection<int, StudyGroup> $studyGroups
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Student> $students
 */
class School extends Model
{
    /** @use HasFactory<\Modules\School\Database\Factories\SchoolFactory> */
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

    /**
     * @return HasMany<SchoolLevel, $this>
     */
    public function levels(): HasMany
    {
        return $this->hasMany(SchoolLevel::class, 'school_id');
    }

    /**
     * @return HasMany<AcademicYear, $this>
     */
    public function academicYears(): HasMany
    {
        return $this->hasMany(AcademicYear::class, 'school_id');
    }

    /**
     * @return HasMany<Department, $this>
     */
    public function departments(): HasMany
    {
        return $this->hasMany(Department::class, 'school_id');
    }

    /**
     * @return HasMany<Subject, $this>
     */
    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class, 'school_id');
    }

    /**
     * @return HasMany<StudyGroup, $this>
     */
    public function studyGroups(): HasMany
    {
        return $this->hasMany(StudyGroup::class, 'school_id');
    }

    /**
     * @return HasMany<Student, $this>
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'school_id');
    }
}
