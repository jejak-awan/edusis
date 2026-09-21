<?php

declare(strict_types=1);

namespace Modules\School\Http\Controllers\Api\HR;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\School\Http\Controllers\Api\Common\BaseController;
use Modules\School\Models\HR\JobApplication;
use Modules\School\Models\HR\JobVacancy;

class CareerController extends BaseController
{
    /**
     * Daftar seluruh lowongan kerja Bursa Kerja Khusus (BKK).
     */
    public function getVacancies(Request $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();

        $query = JobVacancy::where('school_id', $schoolId)
            ->with(['department'])
            ->withCount('applications');

        if ($request->filled('search')) {
            $search = '%'.trim($request->string('search')->toString()).'%';
            $query->where(function ($q) use ($search) {
                $q->where('job_title', 'ilike', $search)
                    ->orWhere('company_name', 'ilike', $search)
                    ->orWhere('location', 'ilike', $search);
            });
        }

        if ($request->filled('job_type')) {
            $query->where('job_type', $request->input('job_type'));
        }

        if ($request->filled('workplace_type')) {
            $query->where('workplace_type', $request->input('workplace_type'));
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->input('department_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $perPage = min($request->integer('per_page', 15), 100);
        $vacancies = $query->orderByDesc('created_at')->paginate($perPage);

        return $this->sendResponse($vacancies, 'Job vacancies retrieved');
    }

    /**
     * Tambah lowongan kerja BKK baru dari mitra industri.
     */
    public function storeVacancy(Request $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();

        $validated = $request->validate([
            'department_id' => 'nullable|integer|exists:sch_acad_departments,id',
            'company_name' => 'required|string|max:150',
            'company_logo_url' => 'nullable|url|max:255',
            'job_title' => 'required|string|max:150',
            'job_type' => 'required|in:fulltime,parttime,internship,freelance,contract',
            'workplace_type' => 'required|in:on_site,remote,hybrid',
            'location' => 'nullable|string|max:150',
            'salary_range' => 'nullable|string|max:100',
            'description' => 'required|string',
            'requirements' => 'nullable|string',
            'benefits' => 'nullable|string',
            'application_deadline' => 'nullable|date',
            'contact_email' => 'nullable|email|max:100',
            'contact_phone' => 'nullable|string|max:30',
            'external_url' => 'nullable|url|max:255',
            'status' => 'nullable|in:draft,published,closed',
        ]);

        $vacancy = JobVacancy::create(array_merge($validated, [
            'school_id' => $schoolId,
            'status' => $validated['status'] ?? 'published',
        ]));

        return $this->sendResponse($vacancy->load('department'), 'Job vacancy created successfully', 201);
    }

    /**
     * Detail lowongan kerja BKK beserta statistik dan lamaran masuk.
     */
    public function showVacancy(int $id): JsonResponse
    {
        $schoolId = $this->getSchoolId();

        $vacancy = JobVacancy::where('school_id', $schoolId)
            ->with(['department', 'applications.student', 'applications.alumni'])
            ->withCount('applications')
            ->findOrFail($id);

        return $this->sendResponse($vacancy, 'Job vacancy detail retrieved');
    }

    /**
     * Edit lowongan kerja BKK.
     */
    public function updateVacancy(Request $request, int $id): JsonResponse
    {
        $schoolId = $this->getSchoolId();

        $vacancy = JobVacancy::where('school_id', $schoolId)->findOrFail($id);

        $validated = $request->validate([
            'department_id' => 'nullable|integer|exists:sch_acad_departments,id',
            'company_name' => 'sometimes|required|string|max:150',
            'company_logo_url' => 'nullable|url|max:255',
            'job_title' => 'sometimes|required|string|max:150',
            'job_type' => 'sometimes|required|in:fulltime,parttime,internship,freelance,contract',
            'workplace_type' => 'sometimes|required|in:on_site,remote,hybrid',
            'location' => 'nullable|string|max:150',
            'salary_range' => 'nullable|string|max:100',
            'description' => 'sometimes|required|string',
            'requirements' => 'nullable|string',
            'benefits' => 'nullable|string',
            'application_deadline' => 'nullable|date',
            'contact_email' => 'nullable|email|max:100',
            'contact_phone' => 'nullable|string|max:30',
            'external_url' => 'nullable|url|max:255',
            'status' => 'sometimes|required|in:draft,published,closed',
        ]);

        $vacancy->update($validated);

        return $this->sendResponse($vacancy->load('department'), 'Job vacancy updated successfully');
    }

    /**
     * Hapus lowongan kerja BKK.
     */
    public function destroyVacancy(int $id): JsonResponse
    {
        $schoolId = $this->getSchoolId();

        $vacancy = JobVacancy::where('school_id', $schoolId)->findOrFail($id);
        $vacancy->delete();

        return $this->sendResponse(null, 'Job vacancy deleted successfully');
    }

    /**
     * Ajukan lamaran pekerjaan untuk suatu lowongan.
     */
    public function applyVacancy(Request $request, int $id): JsonResponse
    {
        $schoolId = $this->getSchoolId();

        $vacancy = JobVacancy::where('school_id', $schoolId)->findOrFail($id);

        if ($vacancy->status !== 'published') {
            return $this->sendError('Lowongan pekerjaan ini tidak sedang membuka penerimaan lamaran.', [], 422);
        }

        $validated = $request->validate([
            'student_id' => 'nullable|integer|exists:sch_std_students,id',
            'alumni_id' => 'nullable|integer|exists:sch_alumnis,id',
            'applicant_name' => 'required|string|max:150',
            'applicant_email' => 'required|email|max:100',
            'applicant_phone' => 'required|string|max:30',
            'resume_url' => 'nullable|url|max:255',
            'portfolio_url' => 'nullable|url|max:255',
            'cover_letter' => 'nullable|string',
        ]);

        $application = JobApplication::create(array_merge($validated, [
            'vacancy_id' => $vacancy->id,
            'status' => 'submitted',
        ]));

        return $this->sendResponse($application, 'Job application submitted successfully', 201);
    }

    /**
     * Daftar pelamar untuk lowongan tertentu.
     */
    public function getApplications(int $vacancyId): JsonResponse
    {
        $schoolId = $this->getSchoolId();

        $vacancy = JobVacancy::where('school_id', $schoolId)->findOrFail($vacancyId);

        $applications = $vacancy->applications()
            ->with(['student.department', 'alumni'])
            ->orderByDesc('created_at')
            ->get();

        return $this->sendResponse($applications, 'Applications retrieved');
    }

    /**
     * Pembaruan status lamaran pekerjaan (seleksi berkas, wawancara, diterima, ditolak).
     */
    public function updateApplicationStatus(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:submitted,reviewed,shortlisted,interview,accepted,rejected',
            'notes' => 'nullable|string',
        ]);

        $application = JobApplication::findOrFail($id);
        $application->update($validated);

        return $this->sendResponse($application, 'Application status updated successfully');
    }

    /**
     * Ringkasan statistik Bursa Kerja Khusus (BKK).
     */
    public function getStats(): JsonResponse
    {
        $schoolId = $this->getSchoolId();

        $totalVacancies = JobVacancy::where('school_id', $schoolId)->count();
        $activeVacancies = JobVacancy::where('school_id', $schoolId)->where('status', 'published')->count();

        $totalApplications = JobApplication::whereHas('vacancy', function ($q) use ($schoolId) {
            $q->where('school_id', $schoolId);
        })->count();

        $acceptedCandidates = JobApplication::whereHas('vacancy', function ($q) use ($schoolId) {
            $q->where('school_id', $schoolId);
        })->where('status', 'accepted')->count();

        return $this->sendResponse([
            'total_vacancies' => $totalVacancies,
            'active_vacancies' => $activeVacancies,
            'total_applications' => $totalApplications,
            'accepted_candidates' => $acceptedCandidates,
        ], 'Career stats retrieved');
    }
}
