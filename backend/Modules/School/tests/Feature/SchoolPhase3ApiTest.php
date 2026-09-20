<?php

declare(strict_types=1);

namespace Modules\School\Tests\Feature;

use Modules\Core\System\Models\User;
use Modules\School\Database\Seeders\SchoolPhase1Seeder;
use Modules\School\Database\Seeders\SchoolPhase2Seeder;
use Modules\School\Database\Seeders\SchoolPhase3Seeder;
use Modules\School\Models\Academic\Assessment;
use Modules\School\Models\Academic\P5Project;
use Modules\School\Models\Academic\StudyGroup;
use Modules\School\Models\Academic\Subject;
use Modules\School\Models\HR\Staff;
use Modules\School\Models\Student\Student;
use Tests\TestCase;

class SchoolPhase3ApiTest extends TestCase
{
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SchoolPhase1Seeder::class);
        $this->seed(SchoolPhase2Seeder::class);
        $this->seed(SchoolPhase3Seeder::class);

        $this->admin = User::firstOrCreate(
            ['email' => 'admin@edusis.local'],
            [
                'name' => 'Super Administrator',
                'password' => bcrypt('password'),
            ]
        );
    }

    public function test_get_assessments_list(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/v1/school/assessments');

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'success',
                'data' => [
                    'data' => [
                        '*' => ['id', 'title', 'type', 'weight', 'max_score'],
                    ],
                ],
            ]);
    }

    public function test_create_assessment_and_store_bulk_grades(): void
    {
        $group = StudyGroup::first();
        $subject = Subject::first();
        $teacher = Staff::first();
        $student = Student::first();

        $this->assertNotNull($group);
        $this->assertNotNull($subject);
        $this->assertNotNull($teacher);
        $this->assertNotNull($student);

        // 1. Create new assessment
        $createRes = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/v1/school/assessments', [
                'study_group_id' => $group->id,
                'subject_id' => $subject->id,
                'teacher_id' => $teacher->id,
                'title' => 'Formatif Baru: Clean Architecture',
                'type' => 'formative',
                'weight' => 1,
                'max_score' => 100,
                'date' => now()->toDateString(),
            ]);

        $createRes->assertStatus(201)
            ->assertJson(['success' => true]);

        $assessmentId = $createRes->json('data.id');

        // 2. Store bulk grades
        $gradeRes = $this->actingAs($this->admin, 'sanctum')
            ->postJson("/api/v1/school/assessments/{$assessmentId}/grades/bulk", [
                'grades' => [
                    [
                        'student_id' => $student->id,
                        'score' => 92.5,
                        'notes' => 'Sangat aktif dalam diskusi kelas',
                    ],
                ],
            ]);

        $gradeRes->assertStatus(200)
            ->assertJson(['success' => true]);

        // 3. Get grades
        $getGradesRes = $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/v1/school/assessments/{$assessmentId}/grades");

        $getGradesRes->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_calculate_and_get_ledger(): void
    {
        $group = StudyGroup::first();
        $this->assertNotNull($group);

        // Calculate
        $calcRes = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/v1/school/reports/calculate', [
                'study_group_id' => $group->id,
            ]);

        $calcRes->assertStatus(200)
            ->assertJson(['success' => true]);

        // Ledger
        $ledgerRes = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/v1/school/reports/ledger?study_group_id='.$group->id);

        $ledgerRes->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'data' => [
                    'study_group',
                    'subjects',
                    'students',
                ],
            ]);
    }

    public function test_get_student_report_card(): void
    {
        $student = Student::first();
        $this->assertNotNull($student);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/v1/school/reports/student/{$student->id}");

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'data' => [
                    'student',
                    'academic_year',
                    'report_cards',
                    'attendance',
                ],
            ]);
    }

    public function test_p5_projects_and_assessments(): void
    {
        $project = P5Project::first();
        $student = Student::first();
        $this->assertNotNull($project);
        $this->assertNotNull($student);

        // Get list
        $listRes = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/v1/school/p5/projects');

        $listRes->assertStatus(200)
            ->assertJson(['success' => true]);

        // Bulk store assessment
        $storeRes = $this->actingAs($this->admin, 'sanctum')
            ->postJson("/api/v1/school/p5/projects/{$project->id}/assessments/bulk", [
                'assessments' => [
                    [
                        'student_id' => $student->id,
                        'dimension' => 'Mandiri',
                        'predicate' => 'SAB',
                        'notes' => 'Menunjukkan inisiatif tinggi dalam pengerjaan proyek',
                    ],
                ],
            ]);

        $storeRes->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_counseling_cases_workflow(): void
    {
        $student = Student::first();
        $counselor = Staff::first();
        $this->assertNotNull($student);
        $this->assertNotNull($counselor);

        // 1. Create case
        $createRes = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/v1/school/counseling/cases', [
                'student_id' => $student->id,
                'counselor_id' => $counselor->id,
                'date' => now()->toDateString(),
                'category' => 'karir',
                'confidentiality' => 'internal',
                'problem_description' => 'Konsultasi pemilihan penjurusan magang industri teknologi',
                'counseling_steps' => 'Identifikasi minat, bakat, dan portofolio coding siswa',
                'solution_plan' => 'Mengarahkan siswa ke mitra industri software house Bandung',
            ]);

        $createRes->assertStatus(201)
            ->assertJson(['success' => true]);

        $caseId = $createRes->json('data.id');

        // 2. Resolve case
        $resolveRes = $this->actingAs($this->admin, 'sanctum')
            ->postJson("/api/v1/school/counseling/cases/{$caseId}/resolve", [
                'follow_up_notes' => 'Siswa telah diterima magang di mitra industri.',
            ]);

        $resolveRes->assertStatus(200)
            ->assertJson(['success' => true]);
    }
}
