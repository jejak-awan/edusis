<?php

declare(strict_types=1);

namespace Modules\School\Models\Voting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\School\Models\HR\Staff;
use Modules\School\Models\Student\Student;

/**
 * @property int $id
 * @property int $event_id
 * @property int $candidate_number
 * @property string $title
 * @property int|null $leader_student_id
 * @property int|null $vice_student_id
 * @property int|null $staff_id
 * @property string|null $vision
 * @property string|null $mission
 * @property string|null $photo_url
 * @property int $vote_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Student|null $leader
 * @property-read Student|null $vice
 * @property-read Staff|null $staff
 */
class VoteCandidate extends Model
{
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

    /**
     * @return BelongsTo<VoteEvent, $this>
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(VoteEvent::class, 'event_id');
    }

    /**
     * @return BelongsTo<Student, $this>
     */
    public function leader(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'leader_student_id');
    }

    /**
     * @return BelongsTo<Student, $this>
     */
    public function vice(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'vice_student_id');
    }

    /**
     * @return BelongsTo<Staff, $this>
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    /**
     * @return HasMany<VoteBallot, $this>
     */
    public function ballots(): HasMany
    {
        return $this->hasMany(VoteBallot::class, 'candidate_id');
    }
}
