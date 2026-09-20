<?php

declare(strict_types=1);

namespace Modules\School\Models\Voting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VoteBallot extends Model
{
    use HasFactory;

    protected $table = 'sch_vote_ballots';

    protected $fillable = [
        'event_id',
        'candidate_id',
        'vote_mode',
        'token_hash',
        'ip_address',
        'device_info',
        'voted_at',
    ];

    protected $casts = [
        'voted_at' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(VoteEvent::class, 'event_id');
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(VoteCandidate::class, 'candidate_id');
    }
}
