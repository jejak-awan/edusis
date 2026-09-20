<?php

declare(strict_types=1);

namespace Modules\School\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\School\Http\Controllers\Api\Common\BaseController;
use Modules\School\Models\Academic\AcademicYear;
use Modules\School\Models\Academic\Department;
use Modules\School\Models\Academic\StudyGroup;
use Modules\School\Models\Institution\School;
use Modules\School\Models\Student\Achievement;
use Modules\School\Models\Student\Student;
use Modules\School\Models\Student\Violation;

class SchoolDashboardController extends BaseController
{
    public function getOverview(Request $request): JsonResponse
    {
        $schoolId = $this->getSchoolId();
        $school = School::find($schoolId);

        $activeYear = AcademicYear::where('school_id', $schoolId)
            ->where('is_active', true)
            ->with('activeSemester')
            ->first();

        $totalStudents = Student::where('school_id', $schoolId)->where('status', 'active')->count();
        $totalMale = Student::where('school_id', $schoolId)->where('status', 'active')->where('gender', 'L')->count();
        $totalFemale = Student::where('school_id', $schoolId)->where('status', 'active')->where('gender', 'P')->count();

        $totalStudyGroups = StudyGroup::where('school_id', $schoolId)->where('is_active', true)->count();
        $totalDepartments = Department::where('school_id', $schoolId)->where('is_active', true)->count();

        $recentViolations = Violation::with(['student.department', 'creditPoint.category'])
            ->latest('violation_date')
            ->limit(5)
            ->get();

        $recentAchievements = Achievement::with(['student.department'])
            ->latest('event_date')
            ->limit(5)
            ->get();

        // Students by department
        $departmentsSummary = Department::where('school_id', $schoolId)
            ->withCount(['students' => function ($q) {
                $q->where('status', 'active');
            }])
            ->get(['id', 'code', 'name', 'students_count']);

        return $this->sendResponse([
            'school' => $school,
            'active_academic_year' => $activeYear,
            'metrics' => [
                'total_students' => $totalStudents,
                'total_male' => $totalMale,
                'total_female' => $totalFemale,
                'total_study_groups' => $totalStudyGroups,
                'total_departments' => $totalDepartments,
            ],
            'departments_summary' => $departmentsSummary,
            'recent_violations' => $recentViolations,
            'recent_achievements' => $recentAchievements,
        ], 'Dashboard overview retrieved');
    }
}
