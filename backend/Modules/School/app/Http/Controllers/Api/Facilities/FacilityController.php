<?php

declare(strict_types=1);

namespace Modules\School\Http\Controllers\Api\Facilities;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\School\Http\Controllers\Api\Common\BaseController;
use Modules\School\Http\Requests\Facilities\StoreAssetRequest;
use Modules\School\Http\Requests\Facilities\StoreMaintenanceTicketRequest;
use Modules\School\Http\Requests\Facilities\StoreRoomRequest;
use Modules\School\Models\Facilities\Asset;
use Modules\School\Models\Facilities\MaintenanceTicket;
use Modules\School\Models\Facilities\Room;
use Modules\School\Services\Facilities\FacilityService;

class FacilityController extends BaseController
{
    public function __construct(
        protected FacilityService $facilityService
    ) {}

    // --- Rooms ---
    public function getRooms(Request $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();
        $query = Room::where('school_id', $schoolId)->withCount('assets');

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('condition')) {
            $query->where('condition', $request->input('condition'));
        }

        $rooms = $query->orderBy('name')->get();

        return $this->sendResponse($rooms, 'Rooms retrieved successfully');
    }

    public function storeRoom(StoreRoomRequest $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();
        $room = $this->facilityService->createRoom($request->validated(), $schoolId);

        return $this->sendResponse($room, 'Room created successfully', 201);
    }

    public function showRoom(int $id): JsonResponse
    {
        $room = Room::with(['assets', 'maintenanceTickets.reporter'])->findOrFail($id);

        return $this->sendResponse($room, 'Room details retrieved');
    }

    public function updateRoom(Request $request, int $id): JsonResponse
    {
        $room = Room::findOrFail($id);

        $validated = $request->validate([
            'code' => 'sometimes|string|max:30',
            'name' => 'sometimes|string|max:100',
            'type' => 'sometimes|in:classroom,laboratory,workshop,library,hall,office,other',
            'capacity' => 'nullable|integer|min:1',
            'floor' => 'nullable|integer|min:1',
            'condition' => 'sometimes|in:baik,rusak_ringan,rusak_berat',
            'description' => 'nullable|string',
        ]);

        $room->update($validated);

        return $this->sendResponse($room, 'Room updated successfully');
    }

    public function deleteRoom(int $id): JsonResponse
    {
        $room = Room::findOrFail($id);
        $room->delete();

        return $this->sendResponse(null, 'Room deleted successfully');
    }

    // --- Assets ---
    public function getAssets(Request $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();
        $query = Asset::where('school_id', $schoolId)->with('room');

        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        if ($request->filled('room_id')) {
            $query->where('room_id', $request->input('room_id'));
        }

        if ($request->filled('condition')) {
            $query->where('condition', $request->input('condition'));
        }

        if ($request->filled('search')) {
            $search = (string) $request->input('search');
            $query->where(function ($q) use ($search): void {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('brand_model', 'like', "%{$search}%");
            });
        }

        $perPage = min((int) ($request->input('per_page', 20)), 100);
        $assets = $query->latest('id')->paginate($perPage);

        return $this->sendResponse($assets, 'Assets retrieved successfully');
    }

    public function storeAsset(StoreAssetRequest $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();
        $asset = $this->facilityService->createAsset($request->validated(), $schoolId);

        return $this->sendResponse($asset, 'Asset registered successfully', 201);
    }

    public function showAsset(int $id): JsonResponse
    {
        $asset = Asset::with(['room', 'maintenanceTickets.reporter'])->findOrFail($id);

        return $this->sendResponse($asset, 'Asset details retrieved');
    }

    public function updateAsset(Request $request, int $id): JsonResponse
    {
        $asset = Asset::findOrFail($id);

        $validated = $request->validate([
            'room_id' => 'nullable|exists:sch_fac_rooms,id',
            'code' => 'sometimes|string|max:50',
            'name' => 'sometimes|string|max:150',
            'category' => 'sometimes|in:elektronik,mebeler,alat_praktik,kendaraan,buku,lainnya',
            'brand_model' => 'nullable|string|max:100',
            'acquisition_year' => 'nullable|integer|min:1990|max:2099',
            'quantity' => 'sometimes|integer|min:1',
            'condition' => 'sometimes|in:baik,rusak_ringan,rusak_berat',
            'source_of_fund' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $asset->update($validated);

        return $this->sendResponse($asset, 'Asset updated successfully');
    }

    public function deleteAsset(int $id): JsonResponse
    {
        $asset = Asset::findOrFail($id);
        $asset->delete();

        return $this->sendResponse(null, 'Asset deleted successfully');
    }

    // --- Maintenance Tickets ---
    public function getTickets(Request $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();
        $query = MaintenanceTicket::where('school_id', $schoolId)
            ->with(['room', 'asset', 'reporter']);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->input('priority'));
        }

        $perPage = min((int) ($request->input('per_page', 20)), 100);
        $tickets = $query->latest('id')->paginate($perPage);

        return $this->sendResponse($tickets, 'Maintenance tickets retrieved successfully');
    }

    public function storeTicket(StoreMaintenanceTicketRequest $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();
        $ticket = $this->facilityService->reportMaintenanceTicket($request->validated(), $schoolId);

        return $this->sendResponse($ticket, 'Maintenance ticket reported successfully', 201);
    }

    public function showTicket(int $id): JsonResponse
    {
        $ticket = MaintenanceTicket::with(['room', 'asset', 'reporter'])->findOrFail($id);

        return $this->sendResponse($ticket, 'Ticket details retrieved');
    }

    public function resolveTicket(Request $request, int $id): JsonResponse
    {
        $ticket = MaintenanceTicket::findOrFail($id);

        $validated = $request->validate([
            'status' => 'nullable|in:resolved,cancelled,in_progress',
            'resolution_notes' => 'required|string',
            'cost' => 'nullable|numeric|min:0',
        ]);

        $resolved = $this->facilityService->resolveMaintenanceTicket($ticket, $validated);

        return $this->sendResponse($resolved, 'Maintenance ticket updated');
    }

    public function deleteTicket(int $id): JsonResponse
    {
        $ticket = MaintenanceTicket::findOrFail($id);
        $ticket->delete();

        return $this->sendResponse(null, 'Maintenance ticket deleted successfully');
    }
}
