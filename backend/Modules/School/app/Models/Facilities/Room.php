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

    /**
     * @return BelongsTo<School, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    /**
     * @return HasMany<Asset, $this>
     */
    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'room_id');
    }

    /**
     * @return HasMany<MaintenanceTicket, $this>
     */
    public function maintenanceTickets(): HasMany
    {
        return $this->hasMany(MaintenanceTicket::class, 'room_id');
    }
}
