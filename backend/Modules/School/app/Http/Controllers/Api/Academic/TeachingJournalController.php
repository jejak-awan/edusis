<?php

declare(strict_types=1);

namespace Modules\School\Http\Controllers\Api\Academic;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\School\Http\Controllers\Api\Common\BaseController;
use Modules\School\Http\Requests\Academic\StoreTeachingJournalRequest;
use Modules\School\Models\Academic\TeachingJournal;
use Modules\School\Services\Academic\AcademicService;

class TeachingJournalController extends BaseController
{
    public function __construct(
        protected AcademicService $academicService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();
        $query = TeachingJournal::where('school_id', $schoolId)
            ->with(['studyGroup', 'subject', 'teacher']);

        if ($request->filled('teacher_id')) {
            $query->where('teacher_id', $request->input('teacher_id'));
        }

        if ($request->filled('study_group_id')) {
            $query->where('study_group_id', $request->input('study_group_id'));
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->input('subject_id'));
        }

        if ($request->filled('date')) {
            $query->where('date', $request->input('date'));
        }

        $perPage = min((int) ($request->input('per_page', 20)), 100);
        $journals = $query->latest('date')->paginate($perPage);

        return $this->sendResponse($journals, 'Teaching journals retrieved');
    }

    public function store(StoreTeachingJournalRequest $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();
        $journal = $this->academicService->recordTeachingJournal($request->validated(), $schoolId);

        return $this->sendResponse(
            $journal->load(['studyGroup', 'subject', 'teacher']),
            'Teaching journal recorded successfully',
            201
        );
    }

    public function show(int $id): JsonResponse
    {
        $journal = TeachingJournal::with(['studyGroup', 'subject', 'teacher'])->findOrFail($id);

        return $this->sendResponse($journal, 'Teaching journal detail retrieved');
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $journal = TeachingJournal::findOrFail($id);

        $validated = $request->validate([
            'teaching_hour' => 'required|string|max:30',
            'topic' => 'required|string|max:200',
            'competency_description' => 'nullable|string',
            'learning_activity' => 'nullable|string',
            'present_count' => 'integer|min:0',
            'sick_count' => 'integer|min:0',
            'permit_count' => 'integer|min:0',
            'absent_count' => 'integer|min:0',
            'notes_reflection' => 'nullable|string',
            'evidence_photo_path' => 'nullable|string',
        ]);

        $journal->update($validated);

        return $this->sendResponse($journal->load(['studyGroup', 'subject', 'teacher']), 'Teaching journal updated');
    }

    public function destroy(int $id): JsonResponse
    {
        $journal = TeachingJournal::findOrFail($id);
        $journal->delete();

        return $this->sendResponse(null, 'Teaching journal deleted');
    }
}
