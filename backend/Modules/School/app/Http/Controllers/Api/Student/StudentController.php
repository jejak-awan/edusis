<?php

declare(strict_types=1);

namespace Modules\School\Http\Controllers\Api\Student;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\School\Http\Controllers\Api\Common\BaseController;
use Modules\School\Http\Requests\Student\StoreStudentRequest;
use Modules\School\Http\Requests\Student\UpdateStudentRequest;
use Modules\School\Models\Student\Student;
use Modules\School\Services\Student\StudentService;

class StudentController extends BaseController
{
    public function __construct(
        protected StudentService $studentService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();
        $query = Student::where('school_id', $schoolId)
            ->with(['level', 'department', 'studyGroups', 'parent']);

        if ($request->filled('search')) {
            $search = '%'.trim((string) $request->input('search')).'%';
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'ilike', $search)
                    ->orWhere('nisn', 'ilike', $search)
                    ->orWhere('nis', 'ilike', $search)
                    ->orWhere('nik', 'ilike', $search);
            });
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->input('department_id'));
        }

        if ($request->filled('school_level_id')) {
            $query->where('school_level_id', $request->input('school_level_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('study_group_id')) {
            $groupId = $request->input('study_group_id');
            $query->whereHas('studyGroups', function ($q) use ($groupId) {
                $q->where('sch_acad_study_groups.id', $groupId);
            });
        }

        $perPage = min((int) ($request->input('per_page', 15)), 100);
        $students = $query->orderBy('full_name')->paginate($perPage);

        return $this->sendResponse($students, 'Students retrieved');
    }

    public function store(StoreStudentRequest $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();
        $student = $this->studentService->registerStudent($request->validated(), $schoolId);

        return $this->sendResponse(
            $student,
            'Student registered successfully',
            201
        );
    }

    public function show(int $id): JsonResponse
    {
        $profile = $this->studentService->getStudentProfile($id);

        return $this->sendResponse($profile, 'Student profile retrieved');
    }

    public function update(UpdateStudentRequest $request, int $id): JsonResponse
    {
        $student = Student::findOrFail($id);
        $updated = $this->studentService->updateStudent($student, $request->validated());

        return $this->sendResponse(
            $updated,
            'Student updated successfully'
        );
    }

    public function destroy(int $id): JsonResponse
    {
        $student = Student::findOrFail($id);
        $student->delete();

        return $this->sendResponse(null, 'Student deleted successfully');
    }
}
