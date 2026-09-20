<?php

declare(strict_types=1);

namespace Modules\School\Models\Academic;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\School\Models\Student\Student;

class Grade extends Model
{
    use HasFactory;

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

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(Assessment::class, 'assessment_id');
    }

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
