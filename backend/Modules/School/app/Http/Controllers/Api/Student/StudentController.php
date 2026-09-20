<?php

declare(strict_types=1);

namespace Modules\School\Http\Controllers\Api\Student;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\School\Http\Controllers\Api\Common\BaseController;
use Modules\School\Models\Academic\StudyGroupMember;
use Modules\School\Models\Institution\School;
use Modules\School\Models\Student\Student;
use Modules\School\Models\Student\StudentParent;

class StudentController extends BaseController
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
        $query = Student::where('school_id', $schoolId)
            ->with(['level', 'department', 'studyGroups', 'parent']);

        // Search by name, NISN, NIS, NIK
        if ($request->filled('search')) {
            $search = '%' . trim((string) $request->input('search')) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'ilike', $search)
                    ->orWhere('nisn', 'ilike', $search)
                    ->orWhere('nis', 'ilike', $search)
                    ->orWhere('nik', 'ilike', $search);
            });
        }

        // Filters
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

    public function store(Request $request): JsonResponse
    {
        $schoolId = $this->resolveSchoolId($request);

        $validated = $request->validate([
            // Student attributes
            'full_name' => 'required|string|max:150',
            'gender' => 'required|in:L,P',
            'nisn' => 'nullable|string|size:10|unique:sch_std_students,nisn',
            'nis' => 'nullable|string|max:20',
            'nik' => 'nullable|string|size:16',
            'school_level_id' => 'nullable|exists:sch_ins_levels,id',
            'department_id' => 'nullable|exists:sch_acad_departments,id',
            'study_group_id' => 'nullable|exists:sch_acad_study_groups,id',
            'place_of_birth' => 'nullable|string|max:100',
            'date_of_birth' => 'nullable|date',
            'religion' => 'nullable|string|max:30',
            'address' => 'nullable|string',
            'rt' => 'nullable|string|max:5',
            'rw' => 'nullable|string|max:5',
            'dusun' => 'nullable|string|max:100',
            'desa_kelurahan' => 'nullable|string|max:100',
            'kecamatan' => 'nullable|string|max:100',
            'kabupaten_kota' => 'nullable|string|max:100',
            'provinsi' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'phone' => 'nullable|string|max:25',
            'email' => 'nullable|email|max:100',
            'entry_year' => 'nullable|string|max:10',
            'status' => 'in:active,graduated,transferred,dropped_out',
            'notes' => 'nullable|string',

            // Parent attributes
            'parent' => 'nullable|array',
            'parent.father_name' => 'nullable|string|max:100',
            'parent.father_nik' => 'nullable|string|size:16',
            'parent.father_occupation' => 'nullable|string|max:100',
            'parent.father_income' => 'nullable|string|max:50',
            'parent.father_phone' => 'nullable|string|max:25',
            'parent.mother_name' => 'nullable|string|max:100',
            'parent.mother_nik' => 'nullable|string|size:16',
            'parent.mother_occupation' => 'nullable|string|max:100',
            'parent.mother_income' => 'nullable|string|max:50',
            'parent.mother_phone' => 'nullable|string|max:25',
            'parent.guardian_name' => 'nullable|string|max:100',
            'parent.guardian_relation' => 'nullable|string|max:50',
            'parent.guardian_phone' => 'nullable|string|max:25',
            'parent.economic_status' => 'nullable|string|max:30',
            'parent.is_pkh' => 'boolean',
            'parent.is_kip' => 'boolean',
            'parent.kip_number' => 'nullable|string|max:50',
            'parent.emergency_phone' => 'nullable|string|max:25',
        ]);

        $studentData = $validated;
        $parentData = $studentData['parent'] ?? [];
        $studyGroupId = $studentData['study_group_id'] ?? null;
        unset($studentData['parent'], $studentData['study_group_id']);

        $studentData['school_id'] = $schoolId;

        $student = DB::transaction(function () use ($studentData, $parentData, $studyGroupId) {
            $student = Student::create($studentData);

            if (!empty($parentData)) {
                $parentData['student_id'] = $student->id;
                StudentParent::create($parentData);
            }

            if ($studyGroupId) {
                StudyGroupMember::create([
                    'study_group_id' => $studyGroupId,
                    'student_id' => $student->id,
                    'status' => 'active',
                ]);
            }

            return $student;
        });

        return $this->sendResponse(
            $student->load(['level', 'department', 'studyGroups', 'parent']),
            'Student registered successfully',
            201
        );
    }

    public function show(int $id): JsonResponse
    {
        $student = Student::with([
            'level',
            'department',
            'studyGroups',
            'parent',
            'creditPoints' => function ($q) {
                $q->with('category')->latest()->limit(20);
            },
            'violations' => function ($q) {
                $q->latest()->limit(10);
            },
            'achievements' => function ($q) {
                $q->latest()->limit(10);
            },
        ])->findOrFail($id);

        $totalScore = $student->total_credit_score;

        return $this->sendResponse([
            'student' => $student,
            'total_credit_score' => $totalScore,
        ], 'Student profile retrieved');
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $student = Student::findOrFail($id);

        $validated = $request->validate([
            'full_name' => 'required|string|max:150',
            'gender' => 'required|in:L,P',
            'nisn' => 'nullable|string|size:10|unique:sch_std_students,nisn,' . $id,
            'nis' => 'nullable|string|max:20',
            'nik' => 'nullable|string|size:16',
            'school_level_id' => 'nullable|exists:sch_ins_levels,id',
            'department_id' => 'nullable|exists:sch_acad_departments,id',
            'place_of_birth' => 'nullable|string|max:100',
            'date_of_birth' => 'nullable|date',
            'religion' => 'nullable|string|max:30',
            'address' => 'nullable|string',
            'rt' => 'nullable|string|max:5',
            'rw' => 'nullable|string|max:5',
            'dusun' => 'nullable|string|max:100',
            'desa_kelurahan' => 'nullable|string|max:100',
            'kecamatan' => 'nullable|string|max:100',
            'kabupaten_kota' => 'nullable|string|max:100',
            'provinsi' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'phone' => 'nullable|string|max:25',
            'email' => 'nullable|email|max:100',
            'entry_year' => 'nullable|string|max:10',
            'status' => 'in:active,graduated,transferred,dropped_out',
            'notes' => 'nullable|string',

            // Parent attributes
            'parent' => 'nullable|array',
            'parent.father_name' => 'nullable|string|max:100',
            'parent.father_nik' => 'nullable|string|size:16',
            'parent.father_occupation' => 'nullable|string|max:100',
            'parent.father_income' => 'nullable|string|max:50',
            'parent.father_phone' => 'nullable|string|max:25',
            'parent.mother_name' => 'nullable|string|max:100',
            'parent.mother_nik' => 'nullable|string|size:16',
            'parent.mother_occupation' => 'nullable|string|max:100',
            'parent.mother_income' => 'nullable|string|max:50',
            'parent.mother_phone' => 'nullable|string|max:25',
            'parent.guardian_name' => 'nullable|string|max:100',
            'parent.guardian_relation' => 'nullable|string|max:50',
            'parent.guardian_phone' => 'nullable|string|max:25',
            'parent.economic_status' => 'nullable|string|max:30',
            'parent.is_pkh' => 'boolean',
            'parent.is_kip' => 'boolean',
            'parent.kip_number' => 'nullable|string|max:50',
            'parent.emergency_phone' => 'nullable|string|max:25',
        ]);

        $studentData = $validated;
        $parentData = $studentData['parent'] ?? null;
        unset($studentData['parent']);

        DB::transaction(function () use ($student, $studentData, $parentData) {
            $student->update($studentData);

            if ($parentData !== null) {
                StudentParent::updateOrCreate(
                    ['student_id' => $student->id],
                    $parentData
                );
            }
        });

        return $this->sendResponse(
            $student->load(['level', 'department', 'studyGroups', 'parent']),
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
