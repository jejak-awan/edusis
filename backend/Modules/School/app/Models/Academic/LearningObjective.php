<?php

declare(strict_types=1);

namespace Modules\School\Models\Academic;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\School\Models\Institution\School;

class LearningObjective extends Model
{
    use HasFactory;

    protected $table = 'sch_acad_learning_objectives';

    protected $fillable = [
        'school_id',
        'subject_id',
        'code',
        'description',
        'phase',
        'order_index',
        'is_active',
    ];

    protected $casts = [
        'order_index' => 'integer',
        'is_active' => 'boolean',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class, 'learning_objective_id');
    }
}
