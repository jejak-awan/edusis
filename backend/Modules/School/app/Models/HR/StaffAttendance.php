<?php

declare(strict_types=1);

namespace Modules\School\Models\HR;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $staff_id
 * @property int|null $shift_id
 * @property \Illuminate\Support\Carbon|null $date
 * @property string|null $check_in
 * @property string|null $check_out
 * @property string $status
 * @property int $late_minutes
 * @property float|null $latitude
 * @property float|null $longitude
 * @property string|null $device_info
 * @property string|null $notes
 * @property string|null $attachment_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Staff|null $staff
 */
class StaffAttendance extends Model
{
    protected $table = 'sch_hr_attendances';

    protected $fillable = [
        'staff_id',
        'shift_id',
        'date',
        'check_in',
        'check_out',
        'status',
        'late_minutes',
        'latitude',
        'longitude',
        'device_info',
        'notes',
        'attachment_path',
    ];

    protected $casts = [
        'date' => 'date',
        'late_minutes' => 'integer',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    /**
     * @return BelongsTo<Staff, $this>
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    /**
     * @return BelongsTo<StaffShift, $this>
     */
    public function shift(): BelongsTo
    {
        return $this->belongsTo(StaffShift::class, 'shift_id');
    }
}
