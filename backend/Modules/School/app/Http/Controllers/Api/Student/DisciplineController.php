<?php

declare(strict_types=1);

namespace Modules\School\Http\Controllers\Api\Student;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\School\Http\Controllers\Api\Common\BaseController;
use Modules\School\Models\Academic\AcademicYear;
use Modules\School\Models\Student\Achievement;
use Modules\School\Models\Student\CreditPoint;
use Modules\School\Models\Student\PointCategory;
use Modules\School\Models\Student\Student;
use Modules\School\Models\Student\Violation;

class DisciplineController extends BaseController
{
    // --- Point Categories ---

    public function getCategories(Request $request): JsonResponse
    {
        $query = PointCategory::query();

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->has('is_active')) {
            $query->where('is_active', filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN));
        }

        $categories = $query->orderBy('type')->orderBy('name')->get();

        return $this->sendResponse($categories, 'Point categories retrieved');
    }

    public function storeCategory(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'type' => 'required|in:pelanggaran,prestasi',
            'default_points' => 'required|integer',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $category = PointCategory::create($validated);

        return $this->sendResponse($category, 'Point category created', 201);
    }

    // --- Record Credit Point Transaction ---

    public function recordPoint(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:sch_std_students,id',
            'category_id' => 'required|exists:sch_std_point_categories,id',
            'points' => 'nullable|integer',
            'description' => 'required|string',
            'date' => 'required|date',
            'admin_notes' => 'nullable|string',

            // Specific fields for violation
            'sanction' => 'nullable|string',
            'sp_level' => 'nullable|in:none,sp1,sp2,sp3',
            'sp_letter_number' => 'nullable|string|max:50',

            // Specific fields for achievement
            'achievement_title' => 'nullable|string|max:150',
            'achievement_level' => 'nullable|in:sekolah,kecamatan,kabupaten,provinsi,nasional,internasional',
            'achievement_rank' => 'nullable|string|max:50',
            'achievement_type' => 'nullable|in:akademik,non_akademik',
            'event_name' => 'nullable|string|max:150',
        ]);

        $category = PointCategory::findOrFail($validated['category_id']);
        $student = Student::findOrFail($validated['student_id']);

        $points = $validated['points'] ?? $category->default_points;

        $user = $request->user();
        $reporterId = $user ? $user->id : null;

        // Current active academic year & semester
        $activeYear = AcademicYear::where('school_id', $student->school_id)
            ->where('is_active', true)
            ->with('activeSemester')
            ->first();

        $academicYearId = $activeYear ? $activeYear->id : null;
        $semesterId = $activeYear && $activeYear->activeSemester ? $activeYear->activeSemester->id : null;

        $result = DB::transaction(function () use (
            $validated,
            $student,
            $category,
            $points,
            $reporterId,
            $academicYearId,
            $semesterId
        ) {
            $creditPoint = CreditPoint::create([
                'student_id' => $student->id,
                'category_id' => $category->id,
                'academic_year_id' => $academicYearId,
                'semester_id' => $semesterId,
                'points' => $points,
                'description' => $validated['description'],
                'date' => $validated['date'],
                'reporter_id' => $reporterId,
                'status' => 'approved',
                'admin_notes' => $validated['admin_notes'] ?? null,
            ]);

            $violation = null;
            $achievement = null;

            if ($category->type === 'pelanggaran') {
                $violation = Violation::create([
                    'student_id' => $student->id,
                    'credit_point_id' => $creditPoint->id,
                    'violation_date' => $validated['date'],
                    'sanction' => $validated['sanction'] ?? null,
                    'sp_level' => $validated['sp_level'] ?? 'none',
                    'sp_letter_number' => $validated['sp_letter_number'] ?? null,
                    'sp_issued_at' => !empty($validated['sp_letter_number']) ? now() : null,
                    'follow_up_notes' => $validated['admin_notes'] ?? null,
                    'is_resolved' => false,
                ]);
            } else {
                $achievement = Achievement::create([
                    'student_id' => $student->id,
                    'credit_point_id' => $creditPoint->id,
                    'title' => $validated['achievement_title'] ?? $category->name,
                    'level' => $validated['achievement_level'] ?? 'sekolah',
                    'rank' => $validated['achievement_rank'] ?? null,
                    'type' => $validated['achievement_type'] ?? 'akademik',
                    'event_name' => $validated['event_name'] ?? $category->name,
                    'event_date' => $validated['date'],
                ]);
            }

            return [
                'credit_point' => $creditPoint->load('category'),
                'violation' => $violation,
                'achievement' => $achievement,
                'current_total_score' => $student->total_credit_score,
            ];
        });

        return $this->sendResponse($result, 'Discipline point recorded successfully', 201);
    }

    // --- Violations List ---

    public function getViolations(Request $request): JsonResponse
    {
        $query = Violation::with(['student.department', 'creditPoint.category']);

        if ($request->filled('sp_level')) {
            $query->where('sp_level', $request->input('sp_level'));
        }

        if ($request->has('is_resolved')) {
            $query->where('is_resolved', filter_var($request->input('is_resolved'), FILTER_VALIDATE_BOOLEAN));
        }

        $perPage = min((int) ($request->input('per_page', 20)), 100);
        $violations = $query->latest('violation_date')->paginate($perPage);

        return $this->sendResponse($violations, 'Violations retrieved');
    }

    public function resolveViolation(Request $request, int $id): JsonResponse
    {
        $violation = Violation::findOrFail($id);
        $violation->update([
            'is_resolved' => true,
            'follow_up_notes' => $request->input('follow_up_notes', 'Telah diselesaikan / dibina oleh guru BK & Kesiswaan.'),
        ]);

        return $this->sendResponse($violation, 'Violation resolved');
    }

    // --- Achievements List ---

    public function getAchievements(Request $request): JsonResponse
    {
        $query = Achievement::with(['student.department', 'creditPoint']);

        if ($request->filled('level')) {
            $query->where('level', $request->input('level'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        $perPage = min((int) ($request->input('per_page', 20)), 100);
        $achievements = $query->latest('event_date')->paginate($perPage);

        return $this->sendResponse($achievements, 'Achievements retrieved');
    }
}
