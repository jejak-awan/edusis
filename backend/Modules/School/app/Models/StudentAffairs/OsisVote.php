<?php

declare(strict_types=1);

namespace Modules\School\Models\StudentAffairs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\School\Models\Student\Student;

/**
 * @property int $id
 * @property int $election_id
 * @property int $candidate_id
 * @property int|null $voter_student_id
 * @property string $token_hash
 * @property \Illuminate\Support\Carbon|null $voted_at
 * @property string|null $ip_address
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read OsisElection|null $election
 * @property-read OsisCandidate|null $candidate
 * @property-read Student|null $voterStudent
 */
class OsisVote extends Model
{
    protected $table = 'sch_std_osis_votes';

    protected $fillable = [
        'election_id',
        'candidate_id',
        'voter_student_id',
        'token_hash',
        'voted_at',
        'ip_address',
    ];

    protected $casts = [
        'voted_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<OsisElection, $this>
     */
    public function election(): BelongsTo
    {
        return $this->belongsTo(OsisElection::class, 'election_id');
    }

    /**
     * @return BelongsTo<OsisCandidate, $this>
     */
    public function candidate(): BelongsTo
    {
        return $this->belongsTo(OsisCandidate::class, 'candidate_id');
    }

    /**
     * @return BelongsTo<Student, $this>
     */
    public function voter(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'voter_student_id');
    }
}
