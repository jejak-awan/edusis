<?php

declare(strict_types=1);

namespace Modules\School\Http\Controllers\Api\Administration;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\School\Http\Controllers\Api\Common\BaseController;
use Modules\School\Http\Requests\Administration\CheckInVisitorRequest;
use Modules\School\Models\Administration\GuestBook;
use Modules\School\Services\Operations\GuestBookService;

class GuestBookController extends BaseController
{
    public function __construct(
        protected GuestBookService $guestBookService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();
        $query = GuestBook::where('school_id', $schoolId);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function ($q) use ($search): void {
                $q->where('visitor_name', 'like', "%{$search}%")
                    ->orWhere('institution', 'like', "%{$search}%")
                    ->orWhere('target_person', 'like', "%{$search}%")
                    ->orWhere('badge_number', 'like', "%{$search}%");
            });
        }

        $perPage = min($request->integer('per_page', 20), 100);
        $guests = $query->latest('check_in_at')->paginate($perPage);

        return $this->sendResponse($guests, 'Guest book records retrieved successfully');
    }

    public function store(CheckInVisitorRequest $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();
        $guest = $this->guestBookService->checkIn($request->validated(), $schoolId);

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
        if ($request->filled('notes')) {
            $guest->notes = $request->string('notes')->toString();
        }
        $updated = $this->guestBookService->checkOut($guest);

        return $this->sendResponse($updated, 'Visitor check-out recorded successfully');
    }

    public function destroy(int $id): JsonResponse
    {
        $guest = GuestBook::findOrFail($id);
        $guest->delete();

        return $this->sendResponse(null, 'Visitor record deleted successfully');
    }
}
