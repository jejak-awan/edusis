<?php

declare(strict_types=1);

namespace Modules\School\Http\Controllers\Api\Student;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\School\Http\Controllers\Api\Common\BaseController;
use Modules\School\Models\Student\Alumni;

class AlumniController extends BaseController
{
    /**
     * Daftar direktori alumni dengan filter pencarian, angkatan, jurusan, dan status karir.
     */
    public function index(Request $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();

        $query = Alumni::where('school_id', $schoolId)
            ->with(['student.department', 'batch']);

        if ($request->filled('search')) {
            $search = '%'.trim($request->string('search')->toString()).'%';
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('full_name', 'ilike', $search)
                    ->orWhere('nisn', 'ilike', $search)
                    ->orWhere('nis', 'ilike', $search);
            })->orWhere('company_or_campus', 'ilike', $search)
                ->orWhere('current_position', 'ilike', $search);
        }

        if ($request->filled('graduation_year')) {
            $query->where('graduation_year', $request->input('graduation_year'));
        }

        if ($request->filled('career_status')) {
            $query->where('career_status', $request->input('career_status'));
        }

        if ($request->filled('department_id')) {
            $deptId = $request->input('department_id');
            $query->whereHas('student', function ($q) use ($deptId) {
                $q->where('department_id', $deptId);
            });
        }

        $perPage = min($request->integer('per_page', 15), 100);
        $alumnis = $query->orderByDesc('graduation_year')
            ->orderBy('id')
            ->paginate($perPage);

        return $this->sendResponse($alumnis, 'Alumni directory retrieved');
    }

    /**
     * Input atau pendaftaran data alumni.
     */
    public function store(Request $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();

        $validated = $request->validate([
            'student_id' => 'required|integer|exists:sch_std_students,id',
            'graduation_batch_id' => 'nullable|integer|exists:sch_graduation_batches,id',
            'graduation_year' => 'required|string|size:4',
            'skl_number' => 'nullable|string|max:100',
            'certificate_number' => 'nullable|string|max:100',
            'average_score' => 'nullable|numeric|between:0,100',
            'career_status' => 'required|in:kuliah,kerja,wirausaha,mencari_kerja,lainnya',
            'company_or_campus' => 'nullable|string|max:150',
            'current_position' => 'nullable|string|max:150',
            'salary_range' => 'nullable|string|max:50',
            'contact_phone' => 'nullable|string|max:25',
            'contact_email' => 'nullable|email|max:100',
            'linkedin_url' => 'nullable|url|max:255',
            'portfolio_url' => 'nullable|url|max:255',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_public' => 'nullable|boolean',
        ]);

        $alumni = Alumni::updateOrCreate(
            [
                'school_id' => $schoolId,
                'student_id' => $validated['student_id'],
            ],
            array_merge($validated, ['school_id' => $schoolId])
        );

        return $this->sendResponse($alumni->load(['student.department', 'batch']), 'Alumni saved successfully', 201);
    }

    /**
     * Detail profil alumni & tracer study.
     */
    public function show(int $id): JsonResponse
    {
        $schoolId = $this->getSchoolId();

        $alumni = Alumni::where('school_id', $schoolId)
            ->with(['student.department', 'batch', 'jobApplications.vacancy'])
            ->findOrFail($id);

        return $this->sendResponse($alumni, 'Alumni detail retrieved');
    }

    /**
     * Pembaruan tracer study alumni.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $schoolId = $this->getSchoolId();

        $alumni = Alumni::where('school_id', $schoolId)->findOrFail($id);

        $validated = $request->validate([
            'career_status' => 'sometimes|required|in:kuliah,kerja,wirausaha,mencari_kerja,lainnya',
            'company_or_campus' => 'nullable|string|max:150',
            'current_position' => 'nullable|string|max:150',
            'salary_range' => 'nullable|string|max:50',
            'contact_phone' => 'nullable|string|max:25',
            'contact_email' => 'nullable|email|max:100',
            'linkedin_url' => 'nullable|url|max:255',
            'portfolio_url' => 'nullable|url|max:255',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_public' => 'nullable|boolean',
        ]);

        $alumni->update($validated);

        return $this->sendResponse($alumni->load(['student.department', 'batch']), 'Alumni tracer updated successfully');
    }

    /**
     * Hapus data alumni.
     */
    public function destroy(int $id): JsonResponse
    {
        $schoolId = $this->getSchoolId();

        $alumni = Alumni::where('school_id', $schoolId)->findOrFail($id);
        $alumni->delete();

        return $this->sendResponse(null, 'Alumni deleted successfully');
    }

    /**
     * Statistik Tracer Study (% Serapan Alumni: Kuliah, Kerja, Wirausaha, Mencari Kerja).
     */
    public function getTracerStats(Request $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();

        $query = Alumni::where('school_id', $schoolId);

        if ($request->filled('graduation_year')) {
            $query->where('graduation_year', $request->input('graduation_year'));
        }

        $total = $query->count();
        $kerja = (clone $query)->where('career_status', 'kerja')->count();
        $kuliah = (clone $query)->where('career_status', 'kuliah')->count();
        $wirausaha = (clone $query)->where('career_status', 'wirausaha')->count();
        $mencariKerja = (clone $query)->where('career_status', 'mencari_kerja')->count();
        $lainnya = (clone $query)->where('career_status', 'lainnya')->count();

        $absorbed = $kerja + $kuliah + $wirausaha;
        $absorptionRate = $total > 0 ? round(($absorbed / $total) * 100, 1) : 0.0;

        return $this->sendResponse([
            'total_alumni' => $total,
            'absorbed_count' => $absorbed,
            'absorption_rate' => $absorptionRate,
            'breakdown' => [
                'kerja' => ['count' => $kerja, 'percentage' => $total > 0 ? round(($kerja / $total) * 100, 1) : 0],
                'kuliah' => ['count' => $kuliah, 'percentage' => $total > 0 ? round(($kuliah / $total) * 100, 1) : 0],
                'wirausaha' => ['count' => $wirausaha, 'percentage' => $total > 0 ? round(($wirausaha / $total) * 100, 1) : 0],
                'mencari_kerja' => ['count' => $mencariKerja, 'percentage' => $total > 0 ? round(($mencariKerja / $total) * 100, 1) : 0],
                'lainnya' => ['count' => $lainnya, 'percentage' => $total > 0 ? round(($lainnya / $total) * 100, 1) : 0],
            ],
        ], 'Tracer study stats retrieved');
    }
}
