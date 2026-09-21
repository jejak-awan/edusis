<?php

declare(strict_types=1);

namespace Modules\School\Http\Controllers\Api\Student;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\School\Http\Controllers\Api\Common\BaseController;
use Modules\School\Models\Academic\StudyGroup;
use Modules\School\Models\Student\Alumni;
use Modules\School\Models\Student\GraduationBatch;
use Modules\School\Models\Student\Student;

class GraduationController extends BaseController
{
    /**
     * Dapatkan daftar seluruh angkatan kelulusan (Batches).
     */
    public function getBatches(Request $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();

        $batches = GraduationBatch::where('school_id', $schoolId)
            ->with(['academicYear'])
            ->withCount('alumnis')
            ->orderByDesc('batch_year')
            ->get();

        return $this->sendResponse($batches, 'Graduation batches retrieved');
    }

    /**
     * Buat angkatan kelulusan baru.
     */
    public function storeBatch(Request $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();

        $validated = $request->validate([
            'academic_year_id' => 'nullable|integer|exists:sch_acad_years,id',
            'batch_year' => 'required|string|size:4',
            'batch_name' => 'required|string|max:100',
            'graduation_date' => 'required|date',
            'decree_number' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
        ]);

        $batch = GraduationBatch::create(array_merge($validated, [
            'school_id' => $schoolId,
            'total_students' => 0,
            'status' => 'draft',
        ]));

        return $this->sendResponse($batch, 'Graduation batch created successfully', 201);
    }

    /**
     * Detail angkatan kelulusan beserta daftar siswa lulusan.
     */
    public function showBatch(int $id): JsonResponse
    {
        $schoolId = $this->getSchoolId();

        $batch = GraduationBatch::where('school_id', $schoolId)
            ->with(['academicYear', 'alumnis.student.department'])
            ->findOrFail($id);

        return $this->sendResponse($batch, 'Graduation batch detail retrieved');
    }

    /**
     * Proses kelulusan massal (rombongan belajar tingkat akhir atau daftar siswa terpilih).
     */
    public function processGraduation(Request $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();

        $validated = $request->validate([
            'graduation_batch_id' => 'required|integer|exists:sch_graduation_batches,id',
            'study_group_id' => 'nullable|integer|exists:sch_acad_study_groups,id',
            'student_ids' => 'nullable|array',
            'student_ids.*' => 'integer|exists:sch_std_students,id',
        ]);

        $batch = GraduationBatch::where('school_id', $schoolId)->findOrFail($validated['graduation_batch_id']);

        $studentIds = [];
        if (! empty($validated['student_ids'])) {
            $studentIds = $validated['student_ids'];
        } elseif (! empty($validated['study_group_id'])) {
            $studyGroup = StudyGroup::with('students')->findOrFail($validated['study_group_id']);
            $studentIds = $studyGroup->students->pluck('id')->toArray();
        }

        if (empty($studentIds)) {
            return $this->sendError('Tidak ada siswa yang dipilih untuk diproses kelulusan.', [], 422);
        }

        $processedCount = 0;

        DB::transaction(function () use ($schoolId, $batch, $studentIds, &$processedCount): void {
            $students = Student::where('school_id', $schoolId)
                ->whereIn('id', $studentIds)
                ->get();

            foreach ($students as $student) {
                // Update status siswa menjadi graduated
                $student->update(['status' => 'graduated']);

                // Nomor SKL unik standar penomoran
                $sklNumber = sprintf('SKL/%s/%04d/%04d', $batch->batch_year, $schoolId, $student->id);

                // Buat atau perbarui catatan alumni
                Alumni::updateOrCreate(
                    [
                        'school_id' => $schoolId,
                        'student_id' => $student->id,
                    ],
                    [
                        'graduation_batch_id' => $batch->id,
                        'graduation_year' => $batch->batch_year,
                        'skl_number' => $sklNumber,
                        'career_status' => 'mencari_kerja',
                        'is_public' => true,
                    ]
                );

                $processedCount++;
            }

            // Update total students pada batch
            $batch->update([
                'total_students' => $batch->alumnis()->count(),
                'status' => 'published',
            ]);
        });

        return $this->sendResponse([
            'batch_id' => $batch->id,
            'processed_students_count' => $processedCount,
        ], "Berhasil memproses kelulusan untuk {$processedCount} siswa.");
    }

    /**
     * Data penerbitan SKL digital untuk siswa.
     */
    public function showSkl(int $studentId): JsonResponse
    {
        $schoolId = $this->getSchoolId();

        $alumni = Alumni::where('school_id', $schoolId)
            ->where('student_id', $studentId)
            ->with(['student.department', 'batch'])
            ->first();

        if (! $alumni) {
            return $this->sendError('Data kelulusan siswa belum diterbitkan atau tidak ditemukan.', [], 404);
        }

        return $this->sendResponse([
            'student' => $alumni->student,
            'batch' => $alumni->batch,
            'skl_number' => $alumni->skl_number,
            'graduation_year' => $alumni->graduation_year,
            'graduation_date' => $alumni->batch?->graduation_date?->format('d F Y'),
            'average_score' => $alumni->average_score,
            'verification_hash' => hash('sha256', $alumni->skl_number.$alumni->student_id.$alumni->graduation_year),
        ], 'SKL data retrieved');
    }
}
