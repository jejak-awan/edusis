<?php

declare(strict_types=1);

namespace Modules\School\Services\StudentAffairs;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\School\Exceptions\SchoolModuleException;
use Modules\School\Models\HR\Staff;
use Modules\School\Models\Student\Student;
use Modules\School\Models\Voting\VoteBallot;
use Modules\School\Models\Voting\VoteCandidate;
use Modules\School\Models\Voting\VoteEvent;
use Modules\School\Models\Voting\VoteVoter;

class VotingEngineService
{
    /**
     * Generate pemilih tetap (DPT) dengan token acak unik 8-karakter.
     */
    public function generateVotersDpt(VoteEvent $event, string $source, ?int $groupId, int $schoolId): array
    {
        $generatedCount = 0;
        $createdVoters = [];

        if (in_array($source, ['students', 'all_students', 'all_school'], true)) {
            $studentQuery = Student::where('school_id', $schoolId)->where('status', 'active');
            if ($groupId) {
                $studentQuery->whereHas('studyGroups', function ($q) use ($groupId) {
                    $q->where('study_group_id', $groupId);
                });
            }

            $students = $studentQuery->get();
            foreach ($students as $student) {
                $existing = VoteVoter::where('event_id', $event->id)
                    ->where('student_id', $student->id)
                    ->first();

                if (!$existing) {
                    $token = VoteVoter::generateUniqueToken($event->id);
                    $voter = VoteVoter::create([
                        'event_id' => $event->id,
                        'voter_type' => 'student',
                        'student_id' => $student->id,
                        'voter_name' => $student->full_name,
                        'voter_identifier' => $student->nisn ?? $student->nis,
                        'token' => $token,
                        'has_voted' => false,
                        'token_generated_at' => Carbon::now(),
                    ]);
                    $createdVoters[] = $voter;
                    $generatedCount++;
                }
            }
        }

        if (in_array($source, ['staff', 'all_school'], true)) {
            $staffMembers = Staff::where('school_id', $schoolId)->get();
            foreach ($staffMembers as $staff) {
                $existing = VoteVoter::where('event_id', $event->id)
                    ->where('staff_id', $staff->id)
                    ->first();

                if (!$existing) {
                    $token = VoteVoter::generateUniqueToken($event->id);
                    $voter = VoteVoter::create([
                        'event_id' => $event->id,
                        'voter_type' => 'staff',
                        'staff_id' => $staff->id,
                        'voter_name' => $staff->full_name,
                        'voter_identifier' => $staff->nip ?? $staff->nuptk ?? ('GTK-' . $staff->id),
                        'token' => $token,
                        'has_voted' => false,
                        'token_generated_at' => Carbon::now(),
                    ]);
                    $createdVoters[] = $voter;
                    $generatedCount++;
                }
            }
        }

        return [
            'event_id' => $event->id,
            'generated_count' => $generatedCount,
            'total_dpt' => $event->voters()->count(),
            'sample_tokens' => array_slice(array_map(fn($v) => [
                'name' => $v->voter_name,
                'token' => $v->token,
                'identifier' => $v->voter_identifier,
            ], $createdVoters), 0, 5),
        ];
    }

    /**
     * Pencoblosan surat suara secara rahasia (Secret Ballot) dan anti pemilih ganda.
     */
    public function castBallot(VoteEvent $event, array $data, ?string $ip = null, ?string $userAgent = null): array
    {
        if ($event->status !== 'active') {
            throw new SchoolModuleException('Pemilihan sedang tidak aktif atau sudah ditutup.', 422);
        }

        $now = Carbon::now();
        if ($now->lt($event->start_date) || $now->gt($event->end_date)) {
            throw new SchoolModuleException('Pemilihan berada di luar jadwal yang ditentukan.', 422);
        }

        $candidate = VoteCandidate::where('event_id', $event->id)
            ->findOrFail($data['candidate_id']);

        $voter = null;
        if (!empty($data['token'])) {
            $tokenInput = strtoupper(trim($data['token']));
            $voter = VoteVoter::where('event_id', $event->id)
                ->where('token', $tokenInput)
                ->first();

            if (!$voter) {
                throw new SchoolModuleException('Token bilik suara tidak valid atau tidak terdaftar.', 422);
            }
        } elseif (!empty($data['student_id'])) {
            $voter = VoteVoter::where('event_id', $event->id)
                ->where('student_id', $data['student_id'])
                ->first();

            if (!$voter) {
                throw new SchoolModuleException('Siswa tidak terdaftar dalam DPT pemilihan ini.', 422);
            }
        }

        if (!$voter) {
            throw new SchoolModuleException('Identitas pemilih tidak ditemukan.', 422);
        }

        if ($voter->has_voted) {
            throw new SchoolModuleException('Hak suara telah digunakan. Setiap pemilih hanya berhak mencoblos 1 kali.', 422);
        }

        return DB::transaction(function () use ($voter, $event, $candidate, $data, $ip, $userAgent, $now) {
            $voter->update([
                'has_voted' => true,
                'voted_at' => $now,
            ]);

            $tokenRaw = $voter->token;
            $tokenHash = hash('sha256', $tokenRaw . $event->id . $now->timestamp);

            VoteBallot::create([
                'event_id' => $event->id,
                'candidate_id' => $candidate->id,
                'vote_mode' => $data['mode'] ?? 'kiosk',
                'token_hash' => $tokenHash,
                'ip_address' => $ip,
                'device_info' => substr((string) $userAgent, 0, 255),
                'voted_at' => $now,
            ]);

            $candidate->increment('vote_count');

            return [
                'voted' => true,
                'receipt_code' => strtoupper(substr($tokenHash, 0, 10)),
                'candidate_name' => $candidate->title,
                'voted_at' => $now->toIso8601String(),
            ];
        });
    }

    /**
     * Hitung rekapitulasi cepat (Quick Count) pemungutan suara secara real-time.
     */
    public function calculateQuickCount(VoteEvent $event): array
    {
        $totalBallots = VoteBallot::where('event_id', $event->id)->count();
        $totalDpt = VoteVoter::where('event_id', $event->id)->count();
        $votedDpt = VoteVoter::where('event_id', $event->id)->where('has_voted', true)->count();
        $turnout = $totalDpt > 0 ? round(($votedDpt / $totalDpt) * 100, 2) : 0;

        $results = $event->candidates->map(function ($c) use ($totalBallots) {
            $pct = $totalBallots > 0 ? round(($c->vote_count / $totalBallots) * 100, 2) : 0;
            return [
                'id' => $c->id,
                'candidate_number' => $c->candidate_number,
                'title' => $c->title,
                'leader_name' => $c->leader?->full_name ?? ($c->staff?->full_name ?? '-'),
                'vice_name' => $c->vice?->full_name ?? '-',
                'vision' => $c->vision,
                'mission' => $c->mission,
                'photo_url' => $c->photo_url,
                'vote_count' => $c->vote_count,
                'percentage' => $pct,
            ];
        });

        return [
            'event_id' => $event->id,
            'event_title' => $event->title,
            'event_type' => $event->type,
            'status' => $event->status,
            'total_ballots' => $totalBallots,
            'total_dpt' => $totalDpt,
            'voted_dpt' => $votedDpt,
            'turnout_percentage' => $turnout,
            'candidates' => $results,
        ];
    }
}
