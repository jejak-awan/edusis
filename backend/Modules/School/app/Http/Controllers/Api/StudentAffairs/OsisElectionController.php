<?php

declare(strict_types=1);

namespace Modules\School\Http\Controllers\Api\StudentAffairs;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\School\Http\Controllers\Api\Common\BaseController;
use Modules\School\Models\Academic\AcademicYear;
use Modules\School\Models\StudentAffairs\OsisCandidate;
use Modules\School\Models\StudentAffairs\OsisElection;
use Modules\School\Models\StudentAffairs\OsisVote;

class OsisElectionController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();
        $query = OsisElection::where('school_id', $schoolId)
            ->with(['candidates.chairman', 'candidates.viceChairman', 'academicYear']);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $elections = $query->latest('id')->get();

        return $this->sendResponse($elections, 'OSIS elections retrieved successfully');
    }

    public function store(Request $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();

        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'period' => 'required|string|max:30',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'in:draft,active,closed',
        ]);

        $activeYear = AcademicYear::where('school_id', $schoolId)->where('is_active', true)->first();
        $academicYearId = $activeYear ? $activeYear->id : 1;

        $election = OsisElection::create(array_merge($validated, [
            'school_id' => $schoolId,
            'academic_year_id' => $academicYearId,
            'status' => $validated['status'] ?? 'draft',
        ]));

        return $this->sendResponse($election, 'OSIS election created successfully', 201);
    }

    public function show(int $id): JsonResponse
    {
        $election = OsisElection::with(['candidates.chairman', 'candidates.viceChairman', 'academicYear'])
            ->findOrFail($id);

        return $this->sendResponse($election, 'OSIS election details retrieved');
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $election = OsisElection::findOrFail($id);

        $validated = $request->validate([
            'title' => 'sometimes|string|max:150',
            'period' => 'sometimes|string|max:30',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after:start_date',
            'status' => 'sometimes|in:draft,active,closed',
        ]);

        $election->update($validated);

        return $this->sendResponse($election, 'OSIS election updated successfully');
    }

    public function addCandidate(Request $request, int $electionId): JsonResponse
    {
        $election = OsisElection::findOrFail($electionId);

        $validated = $request->validate([
            'candidate_number' => 'required|integer|min:1',
            'chairman_student_id' => 'required|exists:sch_std_students,id',
            'vice_chairman_student_id' => 'required|exists:sch_std_students,id',
            'vision' => 'required|string',
            'mission' => 'required|string',
            'photo_url' => 'nullable|string|max:255',
        ]);

        $candidate = OsisCandidate::create(array_merge($validated, [
            'election_id' => $election->id,
            'vote_count' => 0,
        ]));

        return $this->sendResponse($candidate, 'OSIS candidate added successfully', 201);
    }

    public function vote(Request $request, int $electionId): JsonResponse
    {
        $election = OsisElection::findOrFail($electionId);

        if ($election->status !== 'active') {
            return $this->sendError('Pemilihan OSIS ini sedang tidak aktif / ditutup.', [], 400);
        }

        $validated = $request->validate([
            'candidate_id' => 'required|exists:sch_std_osis_candidates,id',
            'voter_student_id' => 'required|exists:sch_std_students,id',
            'token' => 'nullable|string',
        ]);

        $existingVote = OsisVote::where('election_id', $electionId)
            ->where('voter_student_id', $validated['voter_student_id'])
            ->first();

        if ($existingVote) {
            return $this->sendError('Siswa telah menggunakan hak suara pada pemilihan ini.', [], 422);
        }

        $tokenHash = ! empty($validated['token']) ? hash('sha256', (string) $validated['token']) : hash('sha256', (string) microtime());

        $vote = OsisVote::create([
            'election_id' => $electionId,
            'candidate_id' => $validated['candidate_id'],
            'voter_student_id' => $validated['voter_student_id'],
            'token_hash' => $tokenHash,
            'ip_address' => $request->ip(),
            'voted_at' => now(),
        ]);

        $candidate = OsisCandidate::findOrFail((int) $validated['candidate_id']);
        assert($candidate instanceof OsisCandidate);
        $candidate->increment('vote_count');

        return $this->sendResponse([
            'vote_id' => $vote->id,
            'voted_at' => $vote->voted_at,
            'token_receipt' => substr($tokenHash, 0, 12),
        ], 'Suara berhasil dicatat dalam bilik suara digital');
    }

    public function quickCount(int $electionId): JsonResponse
    {
        $election = OsisElection::with(['candidates.chairman', 'candidates.viceChairman'])
            ->findOrFail($electionId);
        assert($election instanceof OsisElection);

        $totalVotes = OsisVote::where('election_id', $electionId)->count();

        $candidatesWithPercentages = $election->candidates->map(function ($candidate) use ($totalVotes): array {
            /** @var OsisCandidate $candidate */
            $percentage = $totalVotes > 0 ? round(($candidate->vote_count / $totalVotes) * 100, 2) : 0;

            return [
                'id' => $candidate->id,
                'candidate_number' => $candidate->candidate_number,
                'chairman_name' => $candidate->chairman ? $candidate->chairman->full_name : '-',
                'vice_chairman_name' => $candidate->viceChairman ? $candidate->viceChairman->full_name : '-',
                'vision' => $candidate->vision,
                'mission' => $candidate->mission,
                'photo_url' => $candidate->photo_url,
                'vote_count' => $candidate->vote_count,
                'percentage' => $percentage,
            ];
        });

        return $this->sendResponse([
            'election_title' => $election->title,
            'period' => $election->period,
            'status' => $election->status,
            'total_votes' => $totalVotes,
            'candidates' => $candidatesWithPercentages,
        ], 'Quick count results calculated');
    }

    public function destroy(int $id): JsonResponse
    {
        $election = OsisElection::findOrFail($id);
        $election->delete();

        return $this->sendResponse(null, 'OSIS election deleted successfully');
    }
}
