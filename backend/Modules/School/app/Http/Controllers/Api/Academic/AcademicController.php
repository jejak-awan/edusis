<?php

declare(strict_types=1);

namespace Modules\School\Http\Controllers\Api\Academic;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\School\Http\Controllers\Api\Common\BaseController;
use Modules\School\Http\Requests\Academic\StoreAcademicYearRequest;
use Modules\School\Http\Requests\Academic\StoreDepartmentRequest;
use Modules\School\Http\Requests\Academic\StoreStudyGroupRequest;
use Modules\School\Http\Requests\Academic\StoreSubjectRequest;
use Modules\School\Models\Academic\AcademicYear;
use Modules\School\Models\Academic\Department;
use Modules\School\Models\Academic\Semester;
use Modules\School\Models\Academic\StudyGroup;
use Modules\School\Models\Academic\StudyGroupMember;
use Modules\School\Models\Academic\Subject;
use Modules\School\Models\Institution\SchoolLevel;
use Modules\School\Services\Academic\AcademicService;

class AcademicController extends BaseController
{
    public function __construct(
        protected AcademicService $academicService
    ) {}

    // --- Academic Years ---

    public function getYears(Request $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();
        $years = AcademicYear::where('school_id', $schoolId)
            ->with(['semesters', 'activeSemester'])
            ->orderByDesc('id')
            ->get();

        return $this->sendResponse($years, 'Academic years retrieved');
    }

    public function storeYear(StoreAcademicYearRequest $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();
        $year = $this->academicService->createAcademicYear($request->validated(), $schoolId);

        return $this->sendResponse($year, 'Academic year created', 201);
    }

    public function activateYear(int $id, Request $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();
        $year = $this->academicService->activateAcademicYear($id, $schoolId);

        return $this->sendResponse($year, 'Academic year activated');
    }

    public function deleteYear(int $id): JsonResponse
    {
        $year = AcademicYear::findOrFail($id);
        $year->delete();

        return $this->sendResponse(null, 'Academic year deleted');
    }

    // --- Semesters ---

    public function getSemesters(int $yearId): JsonResponse
    {
        $semesters = Semester::where('academic_year_id', $yearId)
            ->orderBy('semester_number')
            ->get();

        return $this->sendResponse($semesters, 'Semesters retrieved');
    }

    public function activateSemester(int $id): JsonResponse
    {
        $semester = $this->academicService->activateSemester($id);

        return $this->sendResponse($semester, 'Semester activated');
    }

    // --- Departments ---

    public function getDepartments(Request $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();
        $departments = Department::where('school_id', $schoolId)
            ->withCount('students')
            ->orderBy('code')
            ->get();

        return $this->sendResponse($departments, 'Departments retrieved');
    }

    public function storeDepartment(StoreDepartmentRequest $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();
        $dept = Department::create(array_merge($request->validated(), ['school_id' => $schoolId]));

        return $this->sendResponse($dept, 'Department created', 201);
    }

    public function updateDepartment(Request $request, int $id): JsonResponse
    {
        $dept = Department::findOrFail($id);
        $validated = $request->validate([
            'code' => 'required|string|max:20',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $dept->update($validated);

        return $this->sendResponse($dept, 'Department updated');
    }

    public function deleteDepartment(int $id): JsonResponse
    {
        $dept = Department::findOrFail($id);
        $dept->delete();

        return $this->sendResponse(null, 'Department deleted');
    }

    // --- Subjects ---

    public function getSubjects(Request $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();
        $subjects = Subject::where('school_id', $schoolId)
            ->orderBy('group')
            ->orderBy('name')
            ->get();

        return $this->sendResponse($subjects, 'Subjects retrieved');
    }

    public function storeSubject(StoreSubjectRequest $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();
        $subject = Subject::create(array_merge($request->validated(), ['school_id' => $schoolId]));

        return $this->sendResponse($subject, 'Subject created', 201);
    }

    public function updateSubject(Request $request, int $id): JsonResponse
    {
        $subject = Subject::findOrFail($id);
        $validated = $request->validate([
            'code' => 'nullable|string|max:30',
            'name' => 'required|string|max:100',
            'group' => 'required|string|max:30',
            'kkm' => 'required|integer|min:0|max:100',
            'is_active' => 'boolean',
        ]);

        $subject->update($validated);

        return $this->sendResponse($subject, 'Subject updated');
    }

    public function deleteSubject(int $id): JsonResponse
    {
        $subject = Subject::findOrFail($id);
        $subject->delete();

        return $this->sendResponse(null, 'Subject deleted');
    }

    // --- Study Groups (Rombel) ---

    public function getStudyGroups(Request $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();
        $query = StudyGroup::where('school_id', $schoolId)
            ->with(['level', 'department', 'academicYear'])
            ->withCount('students');

        if ($request->has('academic_year_id')) {
            $query->where('academic_year_id', $request->input('academic_year_id'));
        }

        if ($request->has('department_id')) {
            $query->where('department_id', $request->input('department_id'));
        }

        $studyGroups = $query->orderBy('name')->get();

        return $this->sendResponse($studyGroups, 'Study groups retrieved');
    }

    public function storeStudyGroup(StoreStudyGroupRequest $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();
        $group = StudyGroup::create(array_merge($request->validated(), ['school_id' => $schoolId]));

        return $this->sendResponse($group->load(['level', 'department', 'academicYear']), 'Study group created', 201);
    }

    public function showStudyGroup(int $id): JsonResponse
    {
        $group = StudyGroup::with(['level', 'department', 'academicYear', 'students.parent'])
            ->findOrFail($id);

        return $this->sendResponse($group, 'Study group detail retrieved');
    }

    public function updateStudyGroup(Request $request, int $id): JsonResponse
    {
        $group = StudyGroup::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'school_level_id' => 'nullable|exists:sch_ins_levels,id',
            'department_id' => 'nullable|exists:sch_acad_departments,id',
            'capacity' => 'integer|min:1|max:60',
            'is_active' => 'boolean',
        ]);

        $group->update($validated);

        return $this->sendResponse($group->load(['level', 'department', 'academicYear']), 'Study group updated');
    }

    public function deleteStudyGroup(int $id): JsonResponse
    {
        $group = StudyGroup::findOrFail($id);
        $group->delete();

        return $this->sendResponse(null, 'Study group deleted');
    }

    public function assignStudentToGroup(Request $request, int $groupId): JsonResponse
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:sch_std_students,id',
        ]);

        $member = StudyGroupMember::firstOrCreate([
            'study_group_id' => $groupId,
            'student_id' => $validated['student_id'],
        ], ['status' => 'active']);

        return $this->sendResponse($member, 'Student assigned to study group');
    }

    public function removeStudentFromGroup(int $groupId, int $studentId): JsonResponse
    {
        StudyGroupMember::where('study_group_id', $groupId)
            ->where('student_id', $studentId)
            ->delete();

        return $this->sendResponse(null, 'Student removed from study group');
    }

    // --- Levels ---

    public function getLevels(Request $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();
        $levels = SchoolLevel::where('school_id', $schoolId)->orderBy('grade_level')->get();

        return $this->sendResponse($levels, 'School levels retrieved');
    }
}
