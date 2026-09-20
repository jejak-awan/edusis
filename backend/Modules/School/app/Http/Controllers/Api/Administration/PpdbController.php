<?php

declare(strict_types=1);

namespace Modules\School\Http\Controllers\Api\Administration;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\School\Http\Controllers\Api\Common\BaseController;
use Modules\School\Http\Requests\Administration\StorePpdbRequest;
use Modules\School\Models\Administration\PpdbRegistration;
use Modules\School\Services\Operations\PpdbService;

class PpdbController extends BaseController
{
    public function __construct(
        protected PpdbService $ppdbService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();
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

    public function store(StorePpdbRequest $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();
        $registration = $this->ppdbService->registerApplicant($request->validated(), $schoolId);

        return $this->sendResponse($registration, 'PPDB applicant registered successfully', 201);
    }

    public function show(int $id): JsonResponse
    {
        $registration = PpdbRegistration::with(['department', 'secondChoiceDepartment', 'academicYear', 'verifier'])
            ->findOrFail($id);

        return $this->sendResponse($registration, 'PPDB registration detail retrieved');
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

        $updated = $this->ppdbService->verifyApplicant($registration, $validated);

        return $this->sendResponse($updated, 'PPDB registration status updated');
    }

    public function admit(Request $request, int $id): JsonResponse
    {
        $registration = PpdbRegistration::findOrFail($id);
        $result = $this->ppdbService->admitApplicant($registration);

        return $this->sendResponse($result, 'Applicant admitted and enrolled as active student');
    }

    public function destroy(int $id): JsonResponse
    {
        $registration = PpdbRegistration::findOrFail($id);
        $registration->delete();

        return $this->sendResponse(null, 'PPDB registration deleted successfully');
    }
}
