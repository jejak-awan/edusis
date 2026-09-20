<?php

declare(strict_types=1);

namespace Modules\School\Models\Voting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\School\Models\Academic\AcademicYear;
use Modules\School\Models\Institution\School;

/**
 * @property int $id
 * @property int $school_id
 * @property int $academic_year_id
 * @property string $title
 * @property string $type
 * @property string|null $description
 * @property string $target_audience
 * @property string $voting_mode
 * @property \Illuminate\Support\Carbon|null $start_date
 * @property \Illuminate\Support\Carbon|null $end_date
 * @property string $status
 * @property bool $allow_abstain
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read int $total_votes
 * @property-read int $total_voters
 * @property-read float $turnout_percentage
 * @property-read School $school
 * @property-read AcademicYear $academicYear
 * @property-read \Illuminate\Database\Eloquent\Collection<int, VoteCandidate> $candidates
 * @property-read \Illuminate\Database\Eloquent\Collection<int, VoteVoter> $voters
 * @property-read \Illuminate\Database\Eloquent\Collection<int, VoteBallot> $ballots
 */
class VoteEvent extends Model
{
    /** @use HasFactory<\Modules\School\Database\Factories\VoteEventFactory> */
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

    /**
     * @return BelongsTo<School, $this>
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class, 'school_id');
    }

    /**
     * @return BelongsTo<AcademicYear, $this>
     */
    public function academicYear(): BelongsTo
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    /**
     * @return HasMany<VoteCandidate, $this>
     */
    public function candidates(): HasMany
    {
        return $this->hasMany(VoteCandidate::class, 'event_id')->orderBy('candidate_number');
    }

    /**
     * @return HasMany<VoteVoter, $this>
     */
    public function voters(): HasMany
    {
        return $this->hasMany(VoteVoter::class, 'event_id');
    }

    /**
     * @return HasMany<VoteBallot, $this>
     */
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
