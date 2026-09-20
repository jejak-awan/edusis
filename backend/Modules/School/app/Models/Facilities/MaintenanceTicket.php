<?php

declare(strict_types=1);

namespace Modules\School\Models\Facilities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\School\Models\HR\Staff;
use Modules\School\Models\Institution\School;

class MaintenanceTicket extends Model
{
    use HasFactory;

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

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class, 'asset_id');
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'reporter_staff_id');
    }
}
