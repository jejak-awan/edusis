<?php

declare(strict_types=1);

namespace Modules\School\Http\Controllers\Api\Academic;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\School\Http\Controllers\Api\Common\BaseController;
use Modules\School\Models\Academic\AcademicYear;
use Modules\School\Models\Academic\TeachingJournal;
use Modules\School\Models\Institution\School;

class TeachingJournalController extends BaseController
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

    public function index(Request $request): JsonResponse
    {
        $schoolId = $this->resolveSchoolId($request);
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

    public function store(Request $request): JsonResponse
    {
        $schoolId = $this->resolveSchoolId($request);

        $validated = $request->validate([
            'study_group_id' => 'required|exists:sch_acad_study_groups,id',
            'subject_id' => 'required|exists:sch_acad_subjects,id',
            'teacher_id' => 'required|exists:sch_hr_staff,id',
            'schedule_id' => 'nullable|exists:sch_acad_schedules,id',
            'date' => 'required|date',
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

        $activeYear = AcademicYear::where('school_id', $schoolId)
            ->where('is_active', true)
            ->with('activeSemester')
            ->first();

        $academicYearId = $activeYear ? $activeYear->id : 1;
        $semesterId = $activeYear && $activeYear->activeSemester ? $activeYear->activeSemester->id : 1;

        $journal = TeachingJournal::create(array_merge($validated, [
            'school_id' => $schoolId,
            'academic_year_id' => $academicYearId,
            'semester_id' => $semesterId,
        ]));

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
