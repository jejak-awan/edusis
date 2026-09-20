<?php

declare(strict_types=1);

namespace Modules\School\Models\Integration;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\School\Models\Institution\School;

/**
 * @property int $id
 * @property int $school_id
 * @property string $cbt_url
 * @property string $api_key
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $last_sync_at
 * @property array<string, mixed>|null $sync_stats
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read School $school
 */
class KatampiConfig extends Model
{
    use HasFactory;

    protected $table = 'sch_int_katampi_configs';

    protected $fillable = [
        'school_id',
        'cbt_url',
        'api_key',
        'is_active',
        'last_sync_at',
        'sync_stats',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_sync_at' => 'datetime',
        'sync_stats' => 'array',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }
}
