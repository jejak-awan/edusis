<?php

declare(strict_types=1);

namespace Modules\School\Models\Administration;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\School\Models\Institution\School;

/**
 * @property int $id
 * @property int $school_id
 * @property string $visitor_name
 * @property string|null $institution
 * @property string|null $phone
 * @property string $identity_type
 * @property string|null $identity_number
 * @property string $purpose
 * @property string|null $target_person
 * @property \Illuminate\Support\Carbon|null $check_in_at
 * @property \Illuminate\Support\Carbon|null $check_out_at
 * @property string|null $badge_number
 * @property string $status
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read School|null $school
 */
class GuestBook extends Model
{
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

    /**
     * @return BelongsTo<School, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }
}
