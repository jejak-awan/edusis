<?php

declare(strict_types=1);

namespace Modules\School\Http\Controllers\Api\StudentAffairs;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\School\Http\Controllers\Api\Common\BaseController;
use Modules\School\Models\Academic\AcademicYear;
use Modules\School\Models\Academic\StudyGroup;
use Modules\School\Models\HR\Staff;
use Modules\School\Models\Institution\School;
use Modules\School\Models\Student\Student;
use Modules\School\Models\Voting\VoteBallot;
use Modules\School\Models\Voting\VoteCandidate;
use Modules\School\Models\Voting\VoteEvent;
use Modules\School\Models\Voting\VoteVoter;

class VotingEngineController extends BaseController
{
    private function resolveSchoolId(Request $request): int
    {
        $schoolId = $request->header('X-School-Id');
        if ($schoolId && is_numeric($schoolId)) {
            return (int) $schoolId;
        }

        $school = School::first();
        return $school ? $school->id : 1;
    }

    public function getEvents(Request $request): JsonResponse
    {
        $schoolId = $this->resolveSchoolId($request);
        $query = VoteEvent::where('school_id', $schoolId)
            ->withCount(['candidates', 'voters', 'ballots']);

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $events = $query->orderBy('start_date', 'desc')->get();

        return $this->sendResponse($events, 'Voting events retrieved successfully');
    }

    public function storeEvent(Request $request): JsonResponse
    {
        $schoolId = $this->resolveSchoolId($request);

        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'type' => 'required|in:osis,mpk,ekskul,guru_favorit,polling,komite',
            'description' => 'nullable|string',
            'target_audience' => 'nullable|in:all_students,specific_classes,teachers_only,parents,all',
            'voting_mode' => 'nullable|in:token_kiosk,online_login,hybrid',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'nullable|in:draft,active,closed',
            'allow_abstain' => 'nullable|boolean',
        ]);

        $activeYear = AcademicYear::where('school_id', $schoolId)->where('is_active', true)->first();

        $event = VoteEvent::create(array_merge($validated, [
            'school_id' => $schoolId,
            'academic_year_id' => $activeYear?->id ?? 1,
            'status' => $validated['status'] ?? 'active',
            'voting_mode' => $validated['voting_mode'] ?? 'hybrid',
            'target_audience' => $validated['target_audience'] ?? 'all_students',
            'allow_abstain' => $validated['allow_abstain'] ?? false,
        ]));

        return $this->sendResponse($event, 'Voting event created successfully', 201);
    }

    public function showEvent(int $id): JsonResponse
    {
        $event = VoteEvent::with([
            'candidates.leader',
            'candidates.vice',
            'candidates.staff',
            'academicYear',
        ])
            ->withCount(['candidates', 'voters', 'ballots'])
            ->findOrFail($id);

        return $this->sendResponse($event, 'Voting event details retrieved');
    }

    public function updateEvent(Request $request, int $id): JsonResponse
    {
        $event = VoteEvent::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:150',
            'type' => 'sometimes|in:osis,mpk,ekskul,guru_favorit,polling,komite',
            'description' => 'nullable|string',
            'target_audience' => 'sometimes|in:all_students,specific_classes,teachers_only,parents,all',
            'voting_mode' => 'sometimes|in:token_kiosk,online_login,hybrid',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after:start_date',
            'status' => 'sometimes|in:draft,active,closed',
            'allow_abstain' => 'sometimes|boolean',
        ]);

        $event->update($validated);

        return $this->sendResponse($event, 'Voting event updated successfully');
    }

    public function destroyEvent(int $id): JsonResponse
    {
        $event = VoteEvent::findOrFail($id);
        $event->delete();

        return $this->sendResponse(null, 'Voting event deleted successfully');
    }

    public function addCandidate(Request $request, int $eventId): JsonResponse
    {
        $event = VoteEvent::findOrFail($eventId);

        $validated = $request->validate([
            'candidate_number' => 'required|integer|min:1',
            'title' => 'required|string|max:150',
            'leader_student_id' => 'nullable|exists:sch_std_students,id',
            'vice_student_id' => 'nullable|exists:sch_std_students,id',
            'staff_id' => 'nullable|exists:sch_hr_staff,id',
            'vision' => 'nullable|string',
            'mission' => 'nullable|string',
            'photo_url' => 'nullable|string|max:255',
        ]);

        $candidate = VoteCandidate::updateOrCreate(
            [
                'event_id' => $event->id,
                'candidate_number' => $validated['candidate_number'],
            ],
            $validated
        );

        return $this->sendResponse($candidate, 'Candidate registered successfully', 201);
    }

    public function generateVoters(Request $request, int $eventId): JsonResponse
    {
        $schoolId = $this->resolveSchoolId($request);
        $event = VoteEvent::where('school_id', $schoolId)->findOrFail($eventId);

        $validated = $request->validate([
            'source' => 'required|in:all_students,study_group,staff,all_school',
            'study_group_id' => 'required_if:source,study_group|nullable|exists:sch_acad_study_groups,id',
        ]);

        $source = $validated['source'];
        $generatedCount = 0;
        $createdVoters = [];

        if (in_array($source, ['all_students', 'study_group', 'all_school'], true)) {
            $studentQuery = Student::where('school_id', $schoolId)->whereIn('status', ['active', 'aktif']);
            if ($source === 'study_group' && !empty($validated['study_group_id'])) {
                $groupId = $validated['study_group_id'];
                $studentQuery->whereHas('studyGroups', function ($q) use ($groupId) {
                    $q->where('study_group_id', $groupId);
                });
            }

            $students = $studentQuery->get();
            foreach ($students as $student) {
                // Check if already registered in DPT
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
                        'voter_identifier' => $staff->nip ?? $staff->nuptk ?? 'GTK-' . $staff->id,
                        'token' => $token,
                        'has_voted' => false,
                        'token_generated_at' => Carbon::now(),
                    ]);
                    $createdVoters[] = $voter;
                    $generatedCount++;
                }
            }
        }

        return $this->sendResponse([
            'event_id' => $event->id,
            'generated_count' => $generatedCount,
            'total_dpt' => $event->voters()->count(),
            'sample_tokens' => array_slice(array_map(fn($v) => [
                'name' => $v->voter_name,
                'token' => $v->token,
                'identifier' => $v->voter_identifier,
            ], $createdVoters), 0, 5),
        ], "Berhasil men-generate {$generatedCount} pemilih DPT dengan token unik 8-karakter");
    }

    public function getVoters(Request $request, int $eventId): JsonResponse
    {
        $event = VoteEvent::findOrFail($eventId);
        $query = VoteVoter::where('event_id', $event->id);

        if ($request->filled('has_voted')) {
            $query->where('has_voted', filter_var($request->input('has_voted'), FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where(function ($q) use ($s) {
                $q->where('voter_name', 'like', "%{$s}%")
                    ->orWhere('token', 'like', "%{$s}%")
                    ->orWhere('voter_identifier', 'like', "%{$s}%");
            });
        }

        $voters = $query->orderBy('voter_name')->paginate($request->input('per_page', 50));

        return $this->sendResponse($voters, 'Voter registry (DPT) retrieved successfully');
    }

    public function castVote(Request $request, int $eventId): JsonResponse
    {
        $event = VoteEvent::findOrFail($eventId);

        if ($event->status !== 'active') {
            return $this->sendError('Pemilihan sedang tidak aktif atau sudah ditutup.', [], 422);
        }

        $now = Carbon::now();
        if ($now->lt($event->start_date) || $now->gt($event->end_date)) {
            return $this->sendError('Pemilihan berada di luar jadwal yang ditentukan.', [], 422);
        }

        $validated = $request->validate([
            'candidate_id' => 'required|exists:sch_vote_candidates,id',
            'token' => 'required_without:student_id|nullable|string|max:16',
            'student_id' => 'nullable|exists:sch_std_students,id',
            'mode' => 'nullable|in:kiosk,online',
        ]);

        $candidate = VoteCandidate::where('event_id', $event->id)
            ->findOrFail($validated['candidate_id']);

        $voter = null;
        if (!empty($validated['token'])) {
            $tokenInput = strtoupper(trim($validated['token']));
            $voter = VoteVoter::where('event_id', $event->id)
                ->where('token', $tokenInput)
                ->first();

            if (!$voter) {
                return $this->sendError('Token bilik suara tidak valid atau tidak terdaftar.', [], 422);
            }
        } elseif (!empty($validated['student_id'])) {
            $voter = VoteVoter::where('event_id', $event->id)
                ->where('student_id', $validated['student_id'])
                ->first();

            if (!$voter) {
                return $this->sendError('Siswa tidak terdaftar dalam DPT pemilihan ini.', [], 422);
            }
        }

        if (!$voter) {
            return $this->sendError('Identitas pemilih tidak ditemukan.', [], 422);
        }

        if ($voter->has_voted) {
            return $this->sendError('Hak suara telah digunakan. Setiap pemilih hanya berhak mencoblos 1 kali.', [], 422);
        }

        // Tandai pemilih telah menggunakan hak suaranya
        $voter->update([
            'has_voted' => true,
            'voted_at' => $now,
        ]);

        // Simpan suara ke dalam kotak suara terpisah (Secret Ballot)
        $tokenRaw = $voter->token;
        $tokenHash = hash('sha256', $tokenRaw . $event->id . $now->timestamp);

        $ballot = VoteBallot::create([
            'event_id' => $event->id,
            'candidate_id' => $candidate->id,
            'vote_mode' => $validated['mode'] ?? 'kiosk',
            'token_hash' => $tokenHash,
            'ip_address' => $request->ip(),
            'device_info' => substr((string) $request->userAgent(), 0, 255),
            'voted_at' => $now,
        ]);

        // Increment hitungan suara kandidat
        $candidate->increment('vote_count');

        return $this->sendResponse([
            'voted' => true,
            'receipt_code' => strtoupper(substr($tokenHash, 0, 10)),
            'candidate_name' => $candidate->title,
            'voted_at' => $now->toIso8601String(),
        ], 'Suara berhasil dicoblos dan tercatat di kotak suara digital.');
    }

    public function quickCount(int $eventId): JsonResponse
    {
        $event = VoteEvent::with([
            'candidates.leader',
            'candidates.vice',
            'candidates.staff',
        ])->findOrFail($eventId);

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

        return $this->sendResponse([
            'event_id' => $event->id,
            'event_title' => $event->title,
            'event_type' => $event->type,
            'status' => $event->status,
            'total_ballots' => $totalBallots,
            'total_dpt' => $totalDpt,
            'voted_dpt' => $votedDpt,
            'turnout_percentage' => $turnout,
            'candidates' => $results,
        ], 'Quick count aggregated successfully');
    }
}
