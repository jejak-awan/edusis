<?php

declare(strict_types=1);

namespace Modules\School\Models\Student;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Violation extends Model
{
    use HasFactory;

    protected $table = 'sch_std_violations';

    protected $fillable = [
        'student_id',
        'credit_point_id',
        'violation_date',
        'sanction',
        'sp_level',
        'sp_letter_number',
        'sp_issued_at',
        'follow_up_notes',
        'is_resolved',
    ];

    protected $casts = [
        'violation_date' => 'date',
        'sp_issued_at' => 'date',
        'is_resolved' => 'boolean',
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
