<?php

declare(strict_types=1);

namespace Modules\School\Models\Voting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\School\Models\HR\Staff;
use Modules\School\Models\Student\Student;

class VoteCandidate extends Model
{
    use HasFactory;

    protected $table = 'sch_vote_candidates';

    protected $fillable = [
        'event_id',
        'candidate_number',
        'title',
        'leader_student_id',
        'vice_student_id',
        'staff_id',
        'vision',
        'mission',
        'photo_url',
        'vote_count',
    ];

    protected $casts = [
        'candidate_number' => 'integer',
        'vote_count' => 'integer',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(VoteEvent::class, 'event_id');
    }

    public function leader(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'leader_student_id');
    }

    public function vice(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'vice_student_id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function ballots(): HasMany
    {
        return $this->hasMany(VoteBallot::class, 'candidate_id');
    }
}
