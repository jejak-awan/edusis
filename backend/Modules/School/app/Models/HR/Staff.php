<?php

declare(strict_types=1);

namespace Modules\School\Models\HR;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Core\System\Models\User;
use Modules\School\Models\Academic\Schedule;
use Modules\School\Models\Academic\TeachingJournal;
use Modules\School\Models\Institution\School;

/**
 * @property int $id
 * @property int $school_id
 * @property int|null $user_id
 * @property string $nip
 * @property string|null $nuptk
 * @property string|null $nik
 * @property string $full_name
 * @property string|null $title_prefix
 * @property string|null $title_suffix
 * @property string $gender
 * @property string|null $role_title
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $formal_name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, StaffAttendance> $attendances
 */
class Staff extends Model
{
    /** @use HasFactory<\Modules\School\Database\Factories\StaffFactory> */
    use HasFactory, SoftDeletes;

    protected $table = 'sch_hr_staff';

    protected $fillable = [
        'school_id',
        'user_id',
        'nip',
        'nuptk',
        'nik',
        'full_name',
        'title_prefix',
        'title_suffix',
        'gender',
        'place_of_birth',
        'date_of_birth',
        'religion',
        'employment_status',
        'role_title',
        'last_education',
        'join_date',
        'phone',
        'email',
        'address',
        'avatar_path',
        'is_active',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'certification_status' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * @return BelongsTo<School, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return HasMany<StaffAttendance, $this>
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(StaffAttendance::class, 'staff_id');
    }

    /**
     * @return HasMany<Schedule, $this>
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'teacher_id');
    }

    /**
     * @return HasMany<TeachingJournal, $this>
     */
    public function teachingJournals(): HasMany
    {
        return $this->hasMany(TeachingJournal::class, 'teacher_id');
    }

    /**
     * Nama lengkap bergelar
     */
    public function getFormalNameAttribute(): string
    {
        $prefix = $this->title_prefix ? trim($this->title_prefix).' ' : '';
        $suffix = $this->title_suffix ? ', '.trim($this->title_suffix) : '';

        return $prefix.$this->full_name.$suffix;
    }
}
