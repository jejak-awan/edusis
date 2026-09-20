<?php

declare(strict_types=1);

namespace Modules\School\Models\Voting;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Modules\School\Models\HR\Staff;
use Modules\School\Models\Student\Student;

/**
 * @property int $id
 * @property int $event_id
 * @property string $voter_type
 * @property int|null $student_id
 * @property int|null $staff_id
 * @property string $voter_name
 * @property string|null $voter_identifier
 * @property string|null $token
 * @property bool $has_voted
 * @property \Illuminate\Support\Carbon|null $voted_at
 * @property \Illuminate\Support\Carbon|null $token_generated_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class VoteVoter extends Model
{
    use HasFactory;

    protected $table = 'sch_vote_voters';

    protected $fillable = [
        'event_id',
        'voter_type',
        'student_id',
        'staff_id',
        'voter_name',
        'voter_identifier',
        'token',
        'has_voted',
        'voted_at',
        'token_generated_at',
    ];

    protected $casts = [
        'has_voted' => 'boolean',
        'voted_at' => 'datetime',
        'token_generated_at' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(VoteEvent::class, 'event_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    /**
     * Generate token acak 8 karakter unik untuk bilik suara (ala ja-evoting).
     */
    public static function generateUniqueToken(int $eventId): string
    {
        do {
            $token = strtoupper(Str::random(8));
        } while (self::where('event_id', $eventId)->where('token', $token)->exists());

        return $token;
    }
}
