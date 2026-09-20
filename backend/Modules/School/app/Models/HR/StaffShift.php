<?php

declare(strict_types=1);

namespace Modules\School\Models\HR;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\School\Models\Institution\School;

class StaffShift extends Model
{
    use HasFactory;

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

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(StaffAttendance::class, 'shift_id');
    }
}
