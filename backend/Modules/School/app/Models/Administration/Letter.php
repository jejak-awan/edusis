<?php

declare(strict_types=1);

namespace Modules\School\Models\Administration;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\School\Models\Institution\School;

class Letter extends Model
{
    use HasFactory;

    protected $table = 'sch_adm_letters';

    protected $fillable = [
        'school_id',
        'type',
        'letter_number',
        'reference_number',
        'category',
        'sender',
        'recipient',
        'subject',
        'letter_date',
        'received_or_sent_date',
        'status',
        'disposition_to',
        'disposition_instruction',
        'disposition_status',
        'attachment_path',
        'notes',
    ];

    protected $casts = [
        'letter_date' => 'date',
        'received_or_sent_date' => 'date',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }
}
