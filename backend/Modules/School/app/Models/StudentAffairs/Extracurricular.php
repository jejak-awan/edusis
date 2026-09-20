<?php

declare(strict_types=1);

namespace Modules\School\Models\StudentAffairs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\School\Models\HR\Staff;
use Modules\School\Models\Institution\School;

class Extracurricular extends Model
{
    use HasFactory;

    protected $table = 'sch_std_extracurriculars';

    protected $fillable = [
        'school_id',
        'name',
        'category',
        'advisor_staff_id',
        'schedule_day',
        'location',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    public function advisor(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'advisor_staff_id');
    }
}
