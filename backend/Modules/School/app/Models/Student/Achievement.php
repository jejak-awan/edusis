<?php

declare(strict_types=1);

namespace Modules\School\Models\Student;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Achievement extends Model
{
    use HasFactory;

    protected $table = 'sch_std_achievements';

    protected $fillable = [
        'student_id',
        'credit_point_id',
        'title',
        'level',
        'rank',
        'type',
        'event_name',
        'organizer',
        'event_date',
        'certificate_file',
        'reward_description',
    ];

    protected $casts = [
        'event_date' => 'date',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function creditPoint(): BelongsTo
    {
        return $this->belongsTo(CreditPoint::class, 'credit_point_id');
    }
}
