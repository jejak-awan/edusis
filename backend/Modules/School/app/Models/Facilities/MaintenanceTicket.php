<?php

declare(strict_types=1);

namespace Modules\School\Models\Facilities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\School\Models\HR\Staff;
use Modules\School\Models\Institution\School;

/**
 * @property int $id
 * @property int $school_id
 * @property int|null $room_id
 * @property int|null $asset_id
 * @property int|null $reporter_staff_id
 * @property string $ticket_number
 * @property string $issue_description
 * @property string $priority
 * @property string $status
 * @property string|float|null $cost
 * @property string|null $resolution_notes
 * @property \Illuminate\Support\Carbon|null $resolved_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read School $school
 * @property-read Room|null $room
 * @property-read Asset|null $asset
 * @property-read Staff|null $reporter
 */
class MaintenanceTicket extends Model
{
    protected $table = 'sch_fac_maintenance_tickets';

    protected $fillable = [
        'school_id',
        'room_id',
        'asset_id',
        'reporter_staff_id',
        'ticket_number',
        'issue_description',
        'priority',
        'status',
        'cost',
        'resolution_notes',
        'resolved_at',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'resolved_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<School, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    /**
     * @return BelongsTo<Room, $this>
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    /**
     * @return BelongsTo<Asset, $this>
     */
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    /**
     * @return BelongsTo<Staff, $this>
     */
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'reporter_staff_id');
    }
}
