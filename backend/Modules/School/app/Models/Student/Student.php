<?php

declare(strict_types=1);

namespace Modules\School\Models\Student;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\System\Models\User;
use Modules\School\Models\Academic\Department;
use Modules\School\Models\Academic\StudyGroup;
use Modules\School\Models\Institution\School;
use Modules\School\Models\Institution\SchoolLevel;

/**
 * @property int $id
 * @property int $school_id
 * @property int|null $school_level_id
 * @property int|null $department_id
 * @property int|null $user_id
 * @property string $nisn
 * @property string|null $nis
 * @property string|null $nik
 * @property string $full_name
 * @property string $gender
 * @property string|null $place_of_birth
 * @property string|null $date_of_birth
 * @property string|null $religion
 * @property string|null $address
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read int $total_credit_score
 * @property-read School|null $school
 * @property-read SchoolLevel|null $level
 * @property-read Department|null $department
 * @property-read User|null $user
 * @property-read StudentParent|null $parent
 * @property-read \Illuminate\Database\Eloquent\Collection<int, StudyGroup> $studyGroups
 * @property-read \Illuminate\Database\Eloquent\Collection<int, CreditPoint> $creditPoints
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Violation> $violations
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Achievement> $achievements
 */
class Student extends Model
{
    /** @use HasFactory<\Modules\School\Database\Factories\StudentFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'sch_std_students';

    protected $fillable = [
        'school_id',
        'school_level_id',
        'department_id',
        'user_id',
        'nisn',
        'nis',
        'nik',
        'full_name',
        'gender',
        'place_of_birth',
        'date_of_birth',
        'religion',
        'address',
        'rt',
        'rw',
        'dusun',
        'desa_kelurahan',
        'kecamatan',
        'kabupaten_kota',
        'provinsi',
        'postal_code',
        'phone',
        'email',
        'entry_year',
        'status',
        'notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    /**
     * @return BelongsTo<School, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    /**
     * @return BelongsTo<SchoolLevel, $this>
     */
    public function level(): BelongsTo
    {
        return $this->belongsTo(SchoolLevel::class, 'school_level_id');
    }

    /**
     * @return BelongsTo<Department, $this>
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return HasOne<StudentParent, $this>
     */
    public function parent(): HasOne
    {
        return $this->hasOne(StudentParent::class, 'student_id');
    }

    /**
     * @return BelongsToMany<StudyGroup, $this>
     */
    public function studyGroups(): BelongsToMany
    {
        return $this->belongsToMany(StudyGroup::class, 'sch_acad_study_group_members', 'student_id', 'study_group_id')
            ->withPivot('status')
            ->withTimestamps();
    }

    /**
     * @return HasMany<CreditPoint, $this>
     */
    public function creditPoints(): HasMany
    {
        return $this->hasMany(CreditPoint::class, 'student_id');
    }

    /**
     * @return HasMany<Violation, $this>
     */
    public function violations(): HasMany
    {
        return $this->hasMany(Violation::class, 'student_id');
    }

    /**
     * @return HasMany<Achievement, $this>
     */
    public function achievements(): HasMany
    {
        return $this->hasMany(Achievement::class, 'student_id');
    }

    /**
     * @return HasOne<Alumni, $this>
     */
    public function alumni(): HasOne
    {
        return $this->hasOne(Alumni::class, 'student_id');
    }

    /**
     * Compute current total behavioral credit score
     */
    public function getTotalCreditScoreAttribute(): int
    {
        $baseScore = 100; // Standar default penilaian kedisiplinan siswa
        $points = (int) $this->creditPoints()
            ->where('status', 'approved')
            ->sum('points');

        return $baseScore + $points;
    }
}
