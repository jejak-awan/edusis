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
 * @property string $title
 * @property string $level
 * @property string|null $rank
 * @property string $type
 * @property string|null $event_name
 * @property string|null $organizer
 * @property \Illuminate\Support\Carbon|null $event_date
 * @property string|null $certificate_file
 * @property string|null $reward_description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Student|null $student
 * @property-read CreditPoint|null $creditPoint
 */
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
