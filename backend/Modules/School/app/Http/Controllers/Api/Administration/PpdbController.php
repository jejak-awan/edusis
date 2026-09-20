<?php

declare(strict_types=1);

namespace Modules\School\Http\Controllers\Api\Administration;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\School\Http\Controllers\Api\Common\BaseController;
use Modules\School\Models\Academic\AcademicYear;
use Modules\School\Models\Administration\PpdbRegistration;
use Modules\School\Models\Institution\School;
use Modules\School\Models\Student\Student;

class PpdbController extends BaseController
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
        $query = PpdbRegistration::where('school_id', $schoolId)
            ->with(['department', 'secondChoiceDepartment', 'academicYear', 'verifier']);

        if ($request->filled('track')) {
            $query->where('track', $request->input('track'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->input('department_id'));
        }

        if ($request->filled('search')) {
            $search = (string) $request->input('search');
            $query->where(function ($q) use ($search): void {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('registration_number', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%")
                    ->orWhere('previous_school', 'like', "%{$search}%");
            });
        }

        $perPage = min((int) ($request->input('per_page', 20)), 100);
        $registrations = $query->latest('id')->paginate($perPage);

        return $this->sendResponse($registrations, 'PPDB registrations retrieved successfully');
    }

    public function store(Request $request): JsonResponse
    {
        $schoolId = $this->resolveSchoolId($request);

        $validated = $request->validate([
            'department_id' => 'required|exists:sch_acad_departments,id',
            'second_choice_department_id' => 'nullable|exists:sch_acad_departments,id',
            'track' => 'required|in:zonasi,afirmasi,prestasi_akademik,prestasi_non_akademik,perpindahan_ortu',
            'full_name' => 'required|string|max:150',
            'nisn' => 'nullable|string|max:20',
            'nik' => 'nullable|string|max:20',
            'gender' => 'required|in:L,P',
            'place_of_birth' => 'nullable|string|max:100',
            'date_of_birth' => 'nullable|date',
            'religion' => 'nullable|string|max:30',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string',
            'parent_name' => 'nullable|string|max:150',
            'parent_phone' => 'nullable|string|max:30',
            'parent_job' => 'nullable|string|max:100',
            'parent_income' => 'nullable|string|max:50',
            'previous_school' => 'nullable|string|max:150',
            'average_report_score' => 'nullable|numeric|between:0,100',
            'document_paths' => 'nullable|array',
            'notes' => 'nullable|string',
        ]);

        $activeYear = AcademicYear::where('school_id', $schoolId)->where('is_active', true)->first();
        $academicYearId = $activeYear ? $activeYear->id : 1;

        $year = (int) date('Y');
        $count = PpdbRegistration::where('school_id', $schoolId)->whereYear('created_at', $year)->count();
        $registrationNumber = sprintf('PPDB/%d/%04d', $year, $count + 1);

        $registration = PpdbRegistration::create(array_merge($validated, [
            'school_id' => $schoolId,
            'academic_year_id' => $academicYearId,
            'registration_number' => $registrationNumber,
            'status' => 'submitted',
            'average_report_score' => $validated['average_report_score'] ?? 0,
        ]));

        return $this->sendResponse($registration, 'PPDB applicant registered successfully', 201);
    }

    public function show(int $id): JsonResponse
    {
        $registration = PpdbRegistration::with(['department', 'secondChoiceDepartment', 'academicYear', 'verifier'])
            ->findOrFail($id);

        return $this->sendResponse($registration, 'PPDB registration details retrieved');
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $registration = PpdbRegistration::findOrFail($id);

        $validated = $request->validate([
            'department_id' => 'sometimes|exists:sch_acad_departments,id',
            'second_choice_department_id' => 'nullable|exists:sch_acad_departments,id',
            'track' => 'sometimes|in:zonasi,afirmasi,prestasi_akademik,prestasi_non_akademik,perpindahan_ortu',
            'full_name' => 'sometimes|string|max:150',
            'nisn' => 'nullable|string|max:20',
            'nik' => 'nullable|string|max:20',
            'gender' => 'sometimes|in:L,P',
            'place_of_birth' => 'nullable|string|max:100',
            'date_of_birth' => 'nullable|date',
            'religion' => 'nullable|string|max:30',
            'phone' => 'nullable|string|max:30',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string',
            'parent_name' => 'nullable|string|max:150',
            'parent_phone' => 'nullable|string|max:30',
            'parent_job' => 'nullable|string|max:100',
            'parent_income' => 'nullable|string|max:50',
            'previous_school' => 'nullable|string|max:150',
            'average_report_score' => 'nullable|numeric|between:0,100',
            'document_paths' => 'nullable|array',
            'notes' => 'nullable|string',
        ]);

        $registration->update($validated);

        return $this->sendResponse($registration, 'PPDB registration updated successfully');
    }

    public function verify(Request $request, int $id): JsonResponse
    {
        $registration = PpdbRegistration::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:verified,accepted,rejected',
            'verified_by' => 'nullable|exists:sch_hr_staff,id',
            'notes' => 'nullable|string',
        ]);

        $registration->update([
            'status' => $validated['status'],
            'verified_by' => $validated['verified_by'] ?? $registration->verified_by,
            'verified_at' => now(),
            'notes' => $validated['notes'] ?? $registration->notes,
        ]);

        return $this->sendResponse($registration, 'PPDB registration status updated');
    }

    public function admit(Request $request, int $id): JsonResponse
    {
        $registration = PpdbRegistration::findOrFail($id);

        $school = School::find($registration->school_id);
        $schoolLevelId = $school ? 1 : 1; // Default school level

        // Generate NIS
        $nis = sprintf('%s%04d', date('y'), $registration->id);

        $student = Student::create([
            'school_id' => $registration->school_id,
            'school_level_id' => $schoolLevelId,
            'department_id' => $registration->department_id,
            'nisn' => $registration->nisn,
            'nis' => $nis,
            'nik' => $registration->nik,
            'full_name' => $registration->full_name,
            'gender' => $registration->gender,
            'place_of_birth' => $registration->place_of_birth,
            'date_of_birth' => $registration->date_of_birth,
            'religion' => $registration->religion,
            'phone' => $registration->phone,
            'email' => $registration->email,
            'address' => $registration->address,
            'status' => 'active',
            'admission_date' => now()->toDateString(),
        ]);

        $registration->update([
            'status' => 'accepted',
            'notes' => ($registration->notes ? $registration->notes . ' | ' : '') . 'Diterima menjadi siswa aktif (NIS: ' . $nis . ')',
        ]);

        return $this->sendResponse([
            'registration' => $registration,
            'student' => $student,
        ], 'Applicant admitted and enrolled as active student');
    }

    public function destroy(int $id): JsonResponse
    {
        $registration = PpdbRegistration::findOrFail($id);
        $registration->delete();

        return $this->sendResponse(null, 'PPDB registration deleted successfully');
    }
}
