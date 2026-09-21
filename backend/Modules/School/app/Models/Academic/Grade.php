<?php

declare(strict_types=1);

namespace Modules\School\Models\Academic;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\School\Models\Student\Student;

/**
 * @property int $id
 * @property int $assessment_id
 * @property int $student_id
 * @property float $score
 * @property float|null $remedial_score
 * @property string|null $notes
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read float $effective_score
 * @property-read Assessment|null $assessment
 * @property-read Student|null $student
 */
class Grade extends Model
{
    protected $table = 'sch_acad_grades';

    protected $fillable = [
        'assessment_id',
        'student_id',
        'score',
        'remedial_score',
        'notes',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'remedial_score' => 'decimal:2',
    ];

    /**
     * @return BelongsTo<Assessment, $this>
     */
    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class, 'assessment_id');
    }

    /**
     * @return BelongsTo<Student, $this>
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    // Skor efektif (menggunakan remedial jika ada, atau nilai asli)
    public function getEffectiveScoreAttribute(): float
    {
        return (float) ($this->remedial_score ?? $this->score);
    }
}
