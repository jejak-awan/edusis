<?php

declare(strict_types=1);

namespace Modules\School\Http\Controllers\Api\StudentAffairs;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\School\Exceptions\SchoolModuleException;
use Modules\School\Http\Controllers\Api\Common\BaseController;
use Modules\School\Http\Requests\StudentAffairs\AddVotingCandidateRequest;
use Modules\School\Http\Requests\StudentAffairs\GenerateVotingVotersRequest;
use Modules\School\Http\Requests\StudentAffairs\StoreVotingEventRequest;
use Modules\School\Models\Academic\AcademicYear;
use Modules\School\Models\Voting\VoteCandidate;
use Modules\School\Models\Voting\VoteEvent;
use Modules\School\Models\Voting\VoteVoter;
use Modules\School\Services\StudentAffairs\VotingEngineService;

class VotingEngineController extends BaseController
{
    public function __construct(
        protected VotingEngineService $votingService
    ) {}

    public function getEvents(Request $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();
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

    public function storeEvent(StoreVotingEventRequest $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();
        $validated = $request->validated();

        $activeYear = AcademicYear::where('school_id', $schoolId)->where('is_active', true)->first();
        $academicYearId = $activeYear ? $activeYear->id : 1;

        $event = VoteEvent::create(array_merge($validated, [
            'school_id' => $schoolId,
            'academic_year_id' => $academicYearId,
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

    public function addCandidate(AddVotingCandidateRequest $request, int $eventId): JsonResponse
    {
        $event = VoteEvent::findOrFail($eventId);

        $candidate = VoteCandidate::create(array_merge($request->validated(), [
            'event_id' => $event->id,
            'vote_count' => 0,
        ]));

        return $this->sendResponse(
            $candidate->load(['leader', 'vice', 'staff']),
            'Candidate added successfully to voting event',
            201
        );
    }

    public function updateCandidate(Request $request, int $candidateId): JsonResponse
    {
        $candidate = VoteCandidate::findOrFail($candidateId);

        $validated = $request->validate([
            'candidate_number' => 'sometimes|integer|min:1',
            'title' => 'sometimes|string|max:150',
            'leader_student_id' => 'nullable|exists:sch_std_students,id',
            'vice_student_id' => 'nullable|exists:sch_std_students,id',
            'staff_id' => 'nullable|exists:sch_hr_staff,id',
            'vision' => 'nullable|string',
            'mission' => 'nullable|string',
            'photo_url' => 'nullable|string|max:255',
        ]);

        $candidate->update($validated);

        return $this->sendResponse($candidate, 'Candidate updated successfully');
    }

    public function deleteCandidate(int $candidateId): JsonResponse
    {
        $candidate = VoteCandidate::findOrFail($candidateId);
        $candidate->delete();

        return $this->sendResponse(null, 'Candidate deleted successfully');
    }

    public function generateVoters(GenerateVotingVotersRequest $request, int $eventId): JsonResponse
    {
        $event = VoteEvent::findOrFail($eventId);
        $validated = $request->validated();
        $schoolId = $this->getSchoolId();

        $source = isset($validated['source']) && is_string($validated['source']) ? $validated['source'] : 'all_students';
        $studyGroupId = isset($validated['study_group_id']) && is_numeric($validated['study_group_id']) ? (int) $validated['study_group_id'] : null;

        $result = $this->votingService->generateVotersDpt(
            $event,
            $source,
            $studyGroupId,
            $schoolId
        );

        $count = is_scalar($result['generated_count'] ?? null) ? (string) $result['generated_count'] : '0';

        return $this->sendResponse(
            $result,
            "Berhasil men-generate {$count} pemilih DPT dengan token unik 8-karakter"
        );
    }

    public function getVoters(Request $request, int $eventId): JsonResponse
    {
        $event = VoteEvent::findOrFail($eventId);
        $query = VoteVoter::where('event_id', $event->id);

        if ($request->has('has_voted')) {
            $query->where('has_voted', filter_var($request->input('has_voted'), FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('search')) {
            $search = $request->string('search')->value();
            $query->where(function ($q) use ($search) {
                $q->where('voter_name', 'like', "%{$search}%")
                    ->orWhere('voter_identifier', 'like', "%{$search}%")
                    ->orWhere('token', 'like', "%{$search}%");
            });
        }

        $perPage = min($request->integer('per_page', 25), 200);
        $voters = $query->latest('id')->paginate($perPage);

        return $this->sendResponse($voters, 'Voters list retrieved');
    }

    public function castVote(Request $request, int $eventId): JsonResponse
    {
        $event = VoteEvent::findOrFail($eventId);

        $validated = $request->validate([
            'candidate_id' => 'required|exists:sch_vote_candidates,id',
            'token' => 'required_without:student_id|nullable|string|max:16',
            'student_id' => 'nullable|exists:sch_std_students,id',
            'mode' => 'nullable|in:kiosk,online',
        ]);

        try {
            $result = $this->votingService->castBallot(
                $event,
                $validated,
                $request->ip(),
                (string) $request->userAgent()
            );

            return $this->sendResponse($result, 'Suara berhasil dicoblos dan tercatat di kotak suara digital.');
        } catch (SchoolModuleException $e) {
            return $this->sendError($e->getMessage(), [], $e->getCode() ?: 422);
        }
    }

    public function quickCount(int $eventId): JsonResponse
    {
        $event = VoteEvent::with([
            'candidates.leader',
            'candidates.vice',
            'candidates.staff',
        ])->findOrFail($eventId);

        $result = $this->votingService->calculateQuickCount($event);

        return $this->sendResponse($result, 'Quick count aggregated successfully');
    }
}
