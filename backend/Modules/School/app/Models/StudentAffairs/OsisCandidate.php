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
 * @property int $candidate_number
 * @property int $chairman_student_id
 * @property int $vice_chairman_student_id
 * @property string|null $vision
 * @property string|null $mission
 * @property string|null $photo_url
 * @property int $vote_count
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read OsisElection|null $election
 * @property-read Student|null $chairman
 * @property-read Student|null $viceChairman
 */
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

    /**
     * @return BelongsTo<OsisElection, $this>
     */
    public function election(): BelongsTo
    {
        return $this->belongsTo(OsisElection::class, 'election_id');
    }

    /**
     * @return BelongsTo<Student, $this>
     */
    public function chairman(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'chairman_student_id');
    }

    /**
     * @return BelongsTo<Student, $this>
     */
    public function viceChairman(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'vice_chairman_student_id');
    }
}
