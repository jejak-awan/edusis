<?php

declare(strict_types=1);

namespace Modules\School\Tests\Feature;

use Modules\Core\System\Models\User;
use Modules\School\Database\Seeders\SchoolPhase1Seeder;
use Modules\School\Database\Seeders\SchoolPhase2Seeder;
use Modules\School\Models\Academic\StudyGroup;
use Modules\School\Models\Academic\Subject;
use Modules\School\Models\HR\Staff;
use Modules\School\Models\Student\Student;
use Tests\TestCase;

class SchoolPhase2ApiTest extends TestCase
{
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SchoolPhase1Seeder::class);
        $this->seed(SchoolPhase2Seeder::class);

        $this->admin = User::firstOrCreate(
            ['email' => 'admin@edusis.local'],
            [
                'name' => 'Super Administrator',
                'password' => bcrypt('password'),
            ]
        );
    }

    public function test_get_staff_list(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/v1/school/staff');

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'success',
                'data' => [
                    'data' => [
                        '*' => ['id', 'full_name', 'role_title', 'employment_status'],
                    ],
                ],
            ]);
    }

    public function test_get_student_attendance(): void
    {
        $group = StudyGroup::first();
        $this->assertNotNull($group);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/v1/school/attendance/students?study_group_id='.$group->id.'&date='.now()->toDateString());

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_save_bulk_student_attendance(): void
    {
        $group = StudyGroup::first();
        $student = Student::first();
        $this->assertNotNull($group);
        $this->assertNotNull($student);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/v1/school/attendance/students/bulk', [
                'study_group_id' => $group->id,
                'date' => now()->toDateString(),
                'records' => [
                    [
                        'student_id' => $student->id,
                        'status' => 'H',
                        'notes' => 'Hadir dengan baik',
                    ],
                ],
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_staff_check_in(): void
    {
        $staff = Staff::first();
        $this->assertNotNull($staff);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/v1/school/attendance/staff/check-in', [
                'staff_id' => $staff->id,
                'device_info' => 'Admin Test Console',
                'notes' => 'Check in validasi tes',
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_get_schedules(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/v1/school/schedules');

        $response->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_get_and_store_teaching_journals(): void
    {
        $group = StudyGroup::first();
        $subject = Subject::first();
        $staff = Staff::first();

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/v1/school/journals', [
                'study_group_id' => $group->id,
                'subject_id' => $subject->id,
                'teacher_id' => $staff->id,
                'date' => now()->toDateString(),
                'teaching_hour' => 'Jam 4 - 6',
                'topic' => 'Pengujian Otomatis RESTful API',
                'competency_description' => 'Membuat unit dan feature tests',
                'learning_activity' => 'Siswa memprogram test case',
                'present_count' => 3,
                'sick_count' => 0,
                'permit_count' => 0,
                'absent_count' => 0,
            ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);
    }
}
