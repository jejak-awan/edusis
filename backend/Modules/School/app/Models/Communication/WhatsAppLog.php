<?php

declare(strict_types=1);

namespace Modules\School\Models\Communication;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\School\Models\Institution\School;
use Modules\School\Models\Student\Student;

/**
 * @property int $id
 * @property int $school_id
 * @property int|null $student_id
 * @property string $recipient_phone
 * @property string $recipient_name
 * @property string $message_type
 * @property string $content
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $sent_at
 * @property string|null $error_message
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read School|null $school
 * @property-read Student|null $student
 */
class WhatsAppLog extends Model
{
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

    /**
     * @return BelongsTo<School, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    /**
     * @return BelongsTo<Student, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
