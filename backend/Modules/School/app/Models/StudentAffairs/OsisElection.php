<?php

declare(strict_types=1);

namespace Modules\School\Models\StudentAffairs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\School\Models\Academic\AcademicYear;
use Modules\School\Models\Institution\School;

class OsisElection extends Model
{
    use HasFactory;

    protected $table = 'sch_std_osis_elections';

    protected $fillable = [
        'school_id',
        'academic_year_id',
        'title',
        'period',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function candidates(): HasMany
    {
        return $this->hasMany(OsisCandidate::class, 'election_id')->orderBy('candidate_number');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(OsisVote::class, 'election_id');
    }
}
