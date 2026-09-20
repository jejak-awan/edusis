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

class Student extends Model
{
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

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(SchoolLevel::class, 'school_level_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function parent(): HasOne
    {
        return $this->hasOne(StudentParent::class, 'student_id');
    }

    public function studyGroups(): BelongsToMany
    {
        return $this->belongsToMany(StudyGroup::class, 'sch_acad_study_group_members', 'student_id', 'study_group_id')
            ->withPivot('status')
            ->withTimestamps();
    }

    public function creditPoints(): HasMany
    {
        return $this->hasMany(CreditPoint::class, 'student_id');
    }

    public function violations(): HasMany
    {
        return $this->hasMany(Violation::class, 'student_id');
    }

    public function achievements(): HasMany
    {
        return $this->hasMany(Achievement::class, 'student_id');
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
