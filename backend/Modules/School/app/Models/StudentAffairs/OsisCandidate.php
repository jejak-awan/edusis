<?php

declare(strict_types=1);

namespace Modules\School\Models\StudentAffairs;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\School\Models\Student\Student;

class OsisCandidate extends Model
{
    use HasFactory;

    protected $table = 'sch_std_osis_candidates';

    protected $fillable = [
        'election_id',
        'candidate_number',
        'chairman_student_id',
        'vice_chairman_student_id',
        'vision',
        'mission',
        'photo_url',
        'vote_count',
    ];

    public function election(): BelongsTo
    {
        return $this->belongsTo(OsisElection::class, 'election_id');
    }

    public function chairman(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'chairman_student_id');
    }

    public function viceChairman(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'vice_chairman_student_id');
    }
}
