<?php

declare(strict_types=1);

namespace Modules\School\Models\Academic;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\School\Models\HR\Staff;
use Modules\School\Models\Institution\School;

class P5Project extends Model
{
    use HasFactory;

    protected $table = 'sch_acad_p5_projects';

    protected $fillable = [
        'school_id',
        'academic_year_id',
        'coordinator_id',
        'title',
        'theme',
        'phase',
        'description',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function coordinator(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'coordinator_id');
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(P5Assessment::class, 'project_id');
    }
}
