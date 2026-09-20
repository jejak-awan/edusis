<?php

declare(strict_types=1);

namespace Modules\School\Models\StudentAffairs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\School\Models\Student\Student;

class OsisVote extends Model
{
    use HasFactory;

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

    public function election(): BelongsTo
    {
        return $this->belongsTo(OsisElection::class, 'election_id');
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(OsisCandidate::class, 'candidate_id');
    }

    public function voter(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'voter_student_id');
    }
}
