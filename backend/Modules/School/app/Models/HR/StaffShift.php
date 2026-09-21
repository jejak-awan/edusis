<?php

declare(strict_types=1);

namespace Modules\School\Models\HR;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\School\Models\Institution\School;

/**
 * @property int $id
 * @property int $school_id
 * @property string $name
 * @property string $start_time
 * @property string $end_time
 * @property int $grace_period_minutes
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class StaffShift extends Model
{
    protected $table = 'sch_hr_shifts';

    protected $fillable = [
        'school_id',
        'name',
        'start_time',
        'end_time',
        'grace_period_minutes',
        'is_active',
    ];

    protected $casts = [
        'grace_period_minutes' => 'integer',
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
     * @return HasMany<StaffAttendance, $this>
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(StaffAttendance::class, 'shift_id');
    }
}
