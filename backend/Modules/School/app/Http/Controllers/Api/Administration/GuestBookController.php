<?php

declare(strict_types=1);

namespace Modules\School\Http\Controllers\Api\Administration;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\School\Http\Controllers\Api\Common\BaseController;
use Modules\School\Models\Administration\GuestBook;
use Modules\School\Models\Institution\School;

class GuestBookController extends BaseController
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
        $query = GuestBook::where('school_id', $schoolId);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = (string) $request->input('search');
            $query->where(function ($q) use ($search): void {
                $q->where('visitor_name', 'like', "%{$search}%")
                    ->orWhere('institution', 'like', "%{$search}%")
                    ->orWhere('target_person', 'like', "%{$search}%")
                    ->orWhere('badge_number', 'like', "%{$search}%");
            });
        }

        $perPage = min((int) ($request->input('per_page', 20)), 100);
        $guests = $query->latest('check_in_at')->paginate($perPage);

        return $this->sendResponse($guests, 'Guest book records retrieved successfully');
    }

    public function store(Request $request): JsonResponse
    {
        $schoolId = $this->resolveSchoolId($request);

        $validated = $request->validate([
            'visitor_name' => 'required|string|max:150',
            'institution' => 'nullable|string|max:150',
            'phone' => 'nullable|string|max:30',
            'identity_type' => 'required|in:KTP,SIM,Passport,Lainnya',
            'identity_number' => 'nullable|string|max:50',
            'purpose' => 'required|string|max:255',
            'target_person' => 'required|string|max:150',
            'badge_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        if (empty($validated['badge_number'])) {
            $countToday = GuestBook::where('school_id', $schoolId)
                ->whereDate('check_in_at', now()->toDateString())
                ->count();
            $validated['badge_number'] = sprintf('TAMU-%02d', $countToday + 1);
        }

        $guest = GuestBook::create(array_merge($validated, [
            'school_id' => $schoolId,
            'check_in_at' => now(),
            'status' => 'checked_in',
        ]));

        return $this->sendResponse($guest, 'Visitor check-in recorded successfully', 201);
    }

    public function show(int $id): JsonResponse
    {
        $guest = GuestBook::findOrFail($id);
        return $this->sendResponse($guest, 'Visitor details retrieved');
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $guest = GuestBook::findOrFail($id);

        $validated = $request->validate([
            'visitor_name' => 'sometimes|string|max:150',
            'institution' => 'nullable|string|max:150',
            'phone' => 'nullable|string|max:30',
            'identity_type' => 'sometimes|in:KTP,SIM,Passport,Lainnya',
            'identity_number' => 'nullable|string|max:50',
            'purpose' => 'sometimes|string|max:255',
            'target_person' => 'sometimes|string|max:150',
            'badge_number' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $guest->update($validated);

        return $this->sendResponse($guest, 'Visitor record updated successfully');
    }

    public function checkout(Request $request, int $id): JsonResponse
    {
        $guest = GuestBook::findOrFail($id);

        $guest->update([
            'status' => 'checked_out',
            'check_out_at' => now(),
            'notes' => $request->input('notes', $guest->notes),
        ]);

        return $this->sendResponse($guest, 'Visitor check-out recorded successfully');
    }

    public function destroy(int $id): JsonResponse
    {
        $guest = GuestBook::findOrFail($id);
        $guest->delete();

        return $this->sendResponse(null, 'Visitor record deleted successfully');
    }
}
