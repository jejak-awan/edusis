<?php

declare(strict_types=1);

namespace Modules\School\Models\Student;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $student_id
 * @property int $credit_point_id
 * @property \Illuminate\Support\Carbon $violation_date
 * @property string|null $sanction
 * @property string $sp_level
 * @property string|null $sp_letter_number
 * @property \Illuminate\Support\Carbon|null $sp_issued_at
 * @property string|null $follow_up_notes
 * @property bool $is_resolved
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Student|null $student
 * @property-read CreditPoint|null $creditPoint
 */
class Violation extends Model
{
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

    /**
     * @return BelongsTo<Student, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * @return BelongsTo<CreditPoint, $this>
     */
    public function creditPoint(): BelongsTo
    {
        return $this->belongsTo(CreditPoint::class, 'credit_point_id');
    }
}
