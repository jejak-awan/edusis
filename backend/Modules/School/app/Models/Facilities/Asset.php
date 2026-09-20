<?php

declare(strict_types=1);

namespace Modules\School\Models\Facilities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\School\Models\Institution\School;

class Asset extends Model
{
    use HasFactory;

    protected $table = 'sch_fac_assets';

    protected $fillable = [
        'school_id',
        'room_id',
        'code',
        'name',
        'category',
        'brand_model',
        'acquisition_year',
        'quantity',
        'condition',
        'source_of_fund',
        'notes',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    public function maintenanceTickets(): HasMany
    {
        return $this->hasMany(MaintenanceTicket::class, 'asset_id');
    }
}
