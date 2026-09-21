<?php

declare(strict_types=1);

namespace Modules\School\Models\Academic;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\School\Models\Institution\School;

class Subject extends Model
{
    protected $table = 'sch_acad_subjects';

    protected $fillable = [
        'school_id',
        'code',
        'name',
        'group',
        'kkm',
        'is_active',
    ];

    protected $casts = [
        'kkm' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * @return BelongsTo<School, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }
}
