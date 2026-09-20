<?php

declare(strict_types=1);

namespace Modules\School\Http\Controllers\Api\Administration;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\School\Http\Controllers\Api\Common\BaseController;
use Modules\School\Models\Administration\Letter;
use Modules\School\Models\Institution\School;

class LetterController extends BaseController
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

    private function getRomanMonth(int $month): string
    {
        $map = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
            7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII',
        ];
        return $map[$month] ?? 'I';
    }

    public function index(Request $request): JsonResponse
    {
        $schoolId = $this->resolveSchoolId($request);
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
            $search = (string) $request->input('search');
            $query->where(function ($q) use ($search): void {
                $q->where('letter_number', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('sender', 'like', "%{$search}%")
                    ->orWhere('recipient', 'like', "%{$search}%");
            });
        }

        $perPage = min((int) ($request->input('per_page', 20)), 100);
        $letters = $query->latest('letter_date')->paginate($perPage);

        return $this->sendResponse($letters, 'Letters retrieved successfully');
    }

    public function generateNumber(Request $request): JsonResponse
    {
        $schoolId = $this->resolveSchoolId($request);
        $type = $request->input('type', 'outgoing');
        $category = $request->input('category', 'general');

        $year = (int) date('Y');
        $month = (int) date('n');
        $romanMonth = $this->getRomanMonth($month);

        $codeMap = [
            'invitation' => '005',
            'assignment' => '090',
            'decree' => '421.5',
            'certificate' => '421.3',
            'circular' => '005',
            'general' => '420',
        ];

        $classificationCode = $codeMap[$category] ?? '420';

        $count = Letter::where('school_id', $schoolId)
            ->whereYear('created_at', $year)
            ->count();

        $prefix = $type === 'outgoing' ? 'TU-OUT' : 'TU-IN';
        $number = sprintf('%03d/%s/%s/%s/%d', $count + 1, $prefix, $classificationCode, $romanMonth, $year);

        return $this->sendResponse(['suggested_number' => $number], 'Suggested letter number generated');
    }

    public function store(Request $request): JsonResponse
    {
        $schoolId = $this->resolveSchoolId($request);

        $validated = $request->validate([
            'type' => 'required|in:incoming,outgoing',
            'letter_number' => 'nullable|string|max:100',
            'reference_number' => 'nullable|string|max:100',
            'category' => 'required|in:invitation,assignment,decree,certificate,circular,general',
            'sender' => 'required|string|max:200',
            'recipient' => 'required|string|max:200',
            'subject' => 'required|string|max:255',
            'letter_date' => 'required|date',
            'received_or_sent_date' => 'required|date',
            'status' => 'in:draft,registered,dispatched,archived',
            'disposition_to' => 'nullable|string|max:200',
            'disposition_instruction' => 'nullable|string',
            'disposition_status' => 'in:pending,processed,completed',
            'attachment_path' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        if (empty($validated['letter_number'])) {
            $year = (int) date('Y');
            $romanMonth = $this->getRomanMonth((int) date('n'));
            $count = Letter::where('school_id', $schoolId)->whereYear('created_at', $year)->count();
            $prefix = $validated['type'] === 'outgoing' ? 'TU-OUT' : 'TU-IN';
            $validated['letter_number'] = sprintf('%03d/%s/%s/%d', $count + 1, $prefix, $romanMonth, $year);
        }

        $letter = Letter::create(array_merge($validated, [
            'school_id' => $schoolId,
            'status' => $validated['status'] ?? 'registered',
            'disposition_status' => $validated['disposition_status'] ?? 'pending',
        ]));

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
            'attachment_path' => 'nullable|string|max:255',
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

        $letter->update($validated);

        return $this->sendResponse($letter, 'Disposition sheet updated successfully');
    }

    public function destroy(int $id): JsonResponse
    {
        $letter = Letter::findOrFail($id);
        $letter->delete();

        return $this->sendResponse(null, 'Letter deleted successfully');
    }
}
