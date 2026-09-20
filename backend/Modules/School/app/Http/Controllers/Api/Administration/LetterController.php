<?php

declare(strict_types=1);

namespace Modules\School\Http\Controllers\Api\Administration;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\School\Http\Controllers\Api\Common\BaseController;
use Modules\School\Http\Requests\Administration\StoreLetterRequest;
use Modules\School\Models\Administration\Letter;
use Modules\School\Services\Operations\LetterService;

class LetterController extends BaseController
{
    public function __construct(
        protected LetterService $letterService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();
        $query = Letter::where('school_id', $schoolId);

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('disposition_status')) {
            $query->where('disposition_status', $request->input('disposition_status'));
        }

        if ($request->filled('search')) {
            $search = $request->string('search')->value();
            $query->where(function ($q) use ($search): void {
                $q->where('letter_number', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('sender', 'like', "%{$search}%")
                    ->orWhere('recipient', 'like', "%{$search}%");
            });
        }

        $perPage = min($request->integer('per_page', 20), 100);
        $letters = $query->latest('letter_date')->paginate($perPage);

        return $this->sendResponse($letters, 'Letters retrieved successfully');
    }

    public function generateNumber(Request $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();
        $type = $request->string('type', 'outgoing')->value();
        $category = $request->string('category', 'general')->value();

        $number = $this->letterService->generateSuggestedNumber($schoolId, $type, $category);

        return $this->sendResponse(['suggested_number' => $number], 'Suggested letter number generated');
    }

    public function store(StoreLetterRequest $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();
        $validated = $request->validated();

        $type = isset($validated['type']) && is_string($validated['type']) ? $validated['type'] : 'outgoing';
        $category = isset($validated['category']) && is_string($validated['category']) ? $validated['category'] : 'general';

        if (empty($validated['letter_number'])) {
            $validated['letter_number'] = $this->letterService->generateSuggestedNumber(
                $schoolId,
                $type,
                $category
            );
        }

        $letter = $this->letterService->createLetter($validated, $schoolId);

        return $this->sendResponse($letter, 'Letter recorded successfully', 201);
    }

    public function show(int $id): JsonResponse
    {
        $letter = Letter::findOrFail($id);

        return $this->sendResponse($letter, 'Letter details retrieved');
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $letter = Letter::findOrFail($id);

        $validated = $request->validate([
            'letter_number' => 'sometimes|string|max:100',
            'reference_number' => 'nullable|string|max:100',
            'category' => 'sometimes|in:invitation,assignment,decree,certificate,circular,general',
            'sender' => 'sometimes|string|max:200',
            'recipient' => 'sometimes|string|max:200',
            'subject' => 'sometimes|string|max:255',
            'letter_date' => 'sometimes|date',
            'received_or_sent_date' => 'sometimes|date',
            'status' => 'sometimes|in:draft,registered,dispatched,archived',
            'disposition_to' => 'nullable|string|max:200',
            'disposition_instruction' => 'nullable|string',
            'disposition_status' => 'sometimes|in:pending,processed,completed',
            'file_path' => 'nullable|string|max:500',
            'notes' => 'nullable|string',
        ]);

        $letter->update($validated);

        return $this->sendResponse($letter, 'Letter updated successfully');
    }

    public function updateDisposition(Request $request, int $id): JsonResponse
    {
        $letter = Letter::findOrFail($id);

        $validated = $request->validate([
            'disposition_to' => 'required|string|max:200',
            'disposition_instruction' => 'required|string',
            'disposition_status' => 'required|in:pending,processed,completed',
        ]);

        $updated = $this->letterService->updateDisposition($letter, $validated);

        return $this->sendResponse($updated, 'Disposition sheet updated successfully');
    }

    public function destroy(int $id): JsonResponse
    {
        $letter = Letter::findOrFail($id);
        $letter->delete();

        return $this->sendResponse(null, 'Letter deleted successfully');
    }
}
