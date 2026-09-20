<?php

declare(strict_types=1);

namespace Modules\School\Models\Administration;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\School\Models\Institution\School;

class GuestBook extends Model
{
    use HasFactory;

    protected $table = 'sch_adm_guest_books';

    protected $fillable = [
        'school_id',
        'visitor_name',
        'institution',
        'phone',
        'identity_type',
        'identity_number',
        'purpose',
        'target_person',
        'check_in_at',
        'check_out_at',
        'badge_number',
        'status',
        'notes',
    ];

    protected $casts = [
        'check_in_at' => 'datetime',
        'check_out_at' => 'datetime',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }
}
