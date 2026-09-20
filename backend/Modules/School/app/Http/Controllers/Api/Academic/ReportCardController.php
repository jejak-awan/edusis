<?php

declare(strict_types=1);

namespace Modules\School\Http\Controllers\Api\Academic;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\School\Http\Controllers\Api\Common\BaseController;
use Modules\School\Services\Academic\AssessmentCalculationService;

class ReportCardController extends BaseController
{
    public function __construct(
        protected AssessmentCalculationService $calcService
    ) {}

    public function ledger(Request $request): JsonResponse
    {
        $studyGroupId = (int) $request->input('study_group_id');
        if (!$studyGroupId) {
            return $this->sendError('study_group_id is required', [], 422);
        }

        $schoolId = $this->getSchoolId();
        $ledger = $this->calcService->getLedger($studyGroupId, $schoolId);

        return $this->sendResponse($ledger, 'Ledger data retrieved successfully');
    }

    public function calculate(Request $request): JsonResponse
    {
        $studyGroupId = (int) $request->input('study_group_id');
        if (!$studyGroupId) {
            return $this->sendError('study_group_id is required', [], 422);
        }

        $schoolId = $this->getSchoolId();
        $calculatedCount = $this->calcService->calculateReportCards($studyGroupId, $schoolId);

        return $this->sendResponse([
            'calculated_records' => $calculatedCount,
        ], 'Report cards calculated successfully');
    }

    public function studentReport(Request $request, int $studentId): JsonResponse
    {
        $schoolId = $this->getSchoolId();
        $report = $this->calcService->getStudentReport($studentId, $schoolId);

        return $this->sendResponse($report, 'Student report card retrieved');
    }
}
