<?php

declare(strict_types=1);

namespace Modules\School\Models\Communication;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\School\Models\Institution\School;
use Modules\School\Models\Student\Student;

class WhatsAppLog extends Model
{
    use HasFactory;

    protected $table = 'sch_comm_wa_logs';

    protected $fillable = [
        'school_id',
        'student_id',
        'recipient_phone',
        'recipient_name',
        'message_type',
        'content',
        'status',
        'sent_at',
        'error_message',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
