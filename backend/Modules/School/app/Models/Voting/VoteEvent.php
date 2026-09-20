<?php

declare(strict_types=1);

namespace Modules\School\Models\Voting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\School\Models\Academic\AcademicYear;
use Modules\School\Models\Institution\School;

class VoteEvent extends Model
{
    use HasFactory;

    protected $table = 'sch_vote_events';

    protected $fillable = [
        'school_id',
        'academic_year_id',
        'title',
        'type',
        'description',
        'target_audience',
        'voting_mode',
        'start_date',
        'end_date',
        'status',
        'allow_abstain',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'allow_abstain' => 'boolean',
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
        return $this->hasMany(VoteCandidate::class, 'event_id')->orderBy('candidate_number');
    }

    public function voters(): HasMany
    {
        return $this->hasMany(VoteVoter::class, 'event_id');
    }

    public function ballots(): HasMany
    {
        return $this->hasMany(VoteBallot::class, 'event_id');
    }

    public function getTotalVotesAttribute(): int
    {
        return $this->ballots()->count();
    }

    public function getTotalVotersAttribute(): int
    {
        return $this->voters()->count();
    }

    public function getTurnoutPercentageAttribute(): float
    {
        $total = $this->total_voters;
        if ($total <= 0) {
            return 0.0;
        }

        $voted = $this->voters()->where('has_voted', true)->count();

        return round(($voted / $total) * 100, 2);
    }
}
