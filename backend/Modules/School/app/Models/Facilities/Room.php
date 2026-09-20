<?php

declare(strict_types=1);

namespace Modules\School\Models\Facilities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\School\Models\Institution\School;

class Room extends Model
{
    use HasFactory;

    protected $table = 'sch_fac_rooms';

    protected $fillable = [
        'school_id',
        'code',
        'name',
        'type',
        'capacity',
        'floor',
        'condition',
        'description',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'room_id');
    }

    public function maintenanceTickets(): HasMany
    {
        return $this->hasMany(MaintenanceTicket::class, 'room_id');
    }
}
