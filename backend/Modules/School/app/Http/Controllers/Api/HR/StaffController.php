<?php

declare(strict_types=1);

namespace Modules\School\Http\Controllers\Api\HR;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\School\Http\Controllers\Api\Common\BaseController;
use Modules\School\Http\Requests\HR\StoreStaffRequest;
use Modules\School\Models\HR\Staff;
use Modules\School\Models\HR\StaffShift;

class StaffController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();
        $query = Staff::where('school_id', $schoolId);

        if ($request->filled('search')) {
            $search = '%'.trim($request->string('search')->toString()).'%';
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'ilike', $search)
                    ->orWhere('nip', 'ilike', $search)
                    ->orWhere('nuptk', 'ilike', $search);
            });
        }

        if ($request->filled('employment_status')) {
            $query->where('employment_status', $request->input('employment_status'));
        }

        if ($request->filled('role_title')) {
            $query->where('role_title', 'ilike', '%'.$request->string('role_title')->toString().'%');
        }

        $perPage = min($request->integer('per_page', 20), 100);
        $staff = $query->orderBy('full_name')->paginate($perPage);

        return $this->sendResponse($staff, 'Staff members retrieved');
    }

    public function store(StoreStaffRequest $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();
        $staff = Staff::create(array_merge($request->validated(), ['school_id' => $schoolId]));

        return $this->sendResponse($staff, 'Staff member registered successfully', 201);
    }

    public function show(int $id): JsonResponse
    {
        $staff = Staff::with([
            'schedules.studyGroup',
            'schedules.subject',
            'teachingJournals' => function ($q) {
                $q->latest('date')->limit(10);
            },
        ])->findOrFail($id);

        return $this->sendResponse($staff, 'Staff profile retrieved');
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $staff = Staff::findOrFail($id);

        $validated = $request->validate([
            'full_name' => 'required|string|max:150',
            'gender' => 'required|in:L,P',
            'nip' => 'nullable|string|max:30|unique:sch_hr_staff,nip,'.$id,
            'nuptk' => 'nullable|string|max:20|unique:sch_hr_staff,nuptk,'.$id,
            'nik' => 'nullable|string|size:16',
            'title_prefix' => 'nullable|string|max:30',
            'title_suffix' => 'nullable|string|max:30',
            'place_of_birth' => 'nullable|string|max:100',
            'date_of_birth' => 'nullable|date',
            'religion' => 'nullable|string|max:30',
            'employment_status' => 'required|in:PNS,PPPK,GTY,PTY,Honorer,GTT,PTT',
            'role_title' => 'required|string|max:100',
            'last_education' => 'nullable|string|max:50',
            'major' => 'nullable|string|max:100',
            'certification_status' => 'boolean',
            'sk_number' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:25',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $staff->update($validated);

        return $this->sendResponse($staff, 'Staff profile updated');
    }

    public function destroy(int $id): JsonResponse
    {
        $staff = Staff::findOrFail($id);
        $staff->delete();

        return $this->sendResponse(null, 'Staff member removed');
    }

    // --- Shifts ---

    public function getShifts(Request $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();
        $shifts = StaffShift::where('school_id', $schoolId)->get();

        return $this->sendResponse($shifts, 'Staff shifts retrieved');
    }

    public function storeShift(Request $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();

        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'grace_period_minutes' => 'integer|min:0|max:60',
            'is_active' => 'boolean',
        ]);

        $shift = StaffShift::create(array_merge($validated, ['school_id' => $schoolId]));

        return $this->sendResponse($shift, 'Shift created successfully', 201);
    }
}
