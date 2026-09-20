<?php

declare(strict_types=1);

namespace Modules\School\Tests\Feature;

use Modules\Core\System\Models\User;
use Modules\School\Models\Academic\AcademicYear;
use Modules\School\Models\Academic\Department;
use Modules\School\Models\Academic\StudyGroup;
use Modules\School\Models\Institution\School;
use Modules\School\Models\Student\PointCategory;
use Modules\School\Models\Student\Student;
use Tests\TestCase;

class SchoolPhase1ApiTest extends TestCase
{
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(\Modules\School\Database\Seeders\SchoolPhase1Seeder::class);

        $this->admin = User::firstOrCreate(
            ['email' => 'admin@edusis.local'],
            [
                'name' => 'Super Administrator',
                'password' => bcrypt('password'),
            ]
        );
    }

    public function test_school_ping_returns_ok(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/v1/school/ping');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'JA-EduSIS School Module operational',
            ]);
    }

    public function test_school_dashboard_overview(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/v1/school/dashboard');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'school',
                    'active_academic_year',
                    'metrics' => [
                        'total_students',
                        'total_male',
                        'total_female',
                        'total_study_groups',
                        'total_departments',
                    ],
                    'departments_summary',
                ],
            ]);
    }

    public function test_get_academic_years(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/v1/school/academic/years');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_get_departments(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/v1/school/academic/departments');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_get_students_list(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/v1/school/students');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_record_violation_and_credit_points(): void
    {
        $student = Student::first();
        $category = PointCategory::where('type', 'pelanggaran')->first();

        $this->assertNotNull($student);
        $this->assertNotNull($category);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/v1/school/discipline/record', [
                'student_id' => $student->id,
                'category_id' => $category->id,
                'points' => -10,
                'description' => 'Terlambat masuk sekolah pada jam pertama',
                'date' => now()->toDateString(),
                'sanction' => 'Piket membersihkan perpustakaan',
            ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);
    }
}
