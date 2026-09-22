<?php

declare(strict_types=1);

namespace Modules\School\Tests\Feature;

use Modules\Core\System\Models\User;
use Modules\School\Database\Seeders\SchoolPhase1Seeder;
use Modules\School\Database\Seeders\SchoolPhase2Seeder;
use Modules\School\Database\Seeders\SchoolPhase3Seeder;
use Modules\School\Database\Seeders\SchoolPhase4Seeder;
use Modules\School\Database\Seeders\SchoolPhase5Seeder;
use Modules\School\Models\Academic\Attendance;
use Modules\School\Models\Academic\StudyGroup;
use Modules\School\Models\Academic\Subject;
use Modules\School\Models\Facilities\Asset;
use Modules\School\Models\Facilities\Room;
use Modules\School\Models\HR\Staff;
use Modules\School\Models\Student\Student;
use Tests\TestCase;

class SchoolPhase5ApiTest extends TestCase
{
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SchoolPhase1Seeder::class);
        $this->seed(SchoolPhase2Seeder::class);
        $this->seed(SchoolPhase3Seeder::class);
        $this->seed(SchoolPhase4Seeder::class);
        $this->seed(SchoolPhase5Seeder::class);

        $this->admin = User::firstOrCreate(
            ['email' => 'admin@edusis.local'],
            [
                'name' => 'Super Administrator',
                'password' => bcrypt('password'),
            ]
        );
    }

    public function test_get_rooms_list(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/v1/school/facilities/rooms');

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'code', 'name', 'type', 'capacity', 'floor', 'condition'],
                ],
            ]);
    }

    public function test_create_and_update_room(): void
    {
        $createResponse = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/v1/school/facilities/rooms', [
                'code' => 'LAB-IOT-01',
                'name' => 'Lab IoT & Embedded System',
                'type' => 'laboratory',
                'capacity' => 32,
                'floor' => 2,
                'condition' => 'baik',
                'description' => 'Laboratorium Mikrokontroler dan Robotika',
            ]);

        $createResponse->assertStatus(201)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.code', 'LAB-IOT-01');

        $roomId = $createResponse->json('data.id');

        $updateResponse = $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/v1/school/facilities/rooms/{$roomId}", [
                'name' => 'Lab IoT, Robotika & AI Vokasi',
                'capacity' => 36,
            ]);

        $updateResponse->assertStatus(200)
            ->assertJsonPath('data.name', 'Lab IoT, Robotika & AI Vokasi')
            ->assertJsonPath('data.capacity', 36);
    }

    public function test_get_and_create_asset(): void
    {
        $room = Room::first();

        $createResponse = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/v1/school/facilities/assets', [
                'room_id' => $room?->id,
                'code' => 'AST-SRV-01',
                'name' => 'Server CBT Rackmount Dell PowerEdge',
                'category' => 'elektronik',
                'brand_model' => 'Dell PowerEdge R740',
                'acquisition_year' => 2025,
                'quantity' => 1,
                'condition' => 'baik',
                'source_of_fund' => 'BOS Kinerja',
                'notes' => 'Server dedicated untuk ja-katampi CBT & e-learning',
            ]);

        $createResponse->assertStatus(201)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.code', 'AST-SRV-01');

        $getResponse = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/v1/school/facilities/assets');

        $getResponse->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_create_and_resolve_maintenance_ticket(): void
    {
        $room = Room::first();
        $asset = Asset::first();
        $staff = Staff::first();

        $createResponse = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/v1/school/facilities/tickets', [
                'room_id' => $room->id,
                'asset_id' => $asset?->id,
                'reporter_staff_id' => $staff->id,
                'issue_description' => 'Port LAN pada workstation 04 mengalami kendala koneksi putus-nyambung',
                'priority' => 'sedang',
            ]);

        $createResponse->assertStatus(201)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.status', 'open');

        $ticketId = $createResponse->json('data.id');

        $resolveResponse = $this->actingAs($this->admin, 'sanctum')
            ->postJson("/api/v1/school/facilities/tickets/{$ticketId}/resolve", [
                'resolution_notes' => 'Konektor RJ45 dipasang ulang dengan crimping baru. Jaringan kembali normal 1Gbps.',
                'cost' => 25000,
            ]);

        $resolveResponse->assertStatus(200)
            ->assertJsonPath('data.status', 'resolved')
            ->assertJsonPath('data.cost', '25000.00');

        // Verify maintenance alias route
        $aliasResponse = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/v1/school/facilities/maintenance');
        $aliasResponse->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_katampi_config_and_update(): void
    {
        $getResponse = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/v1/school/integrations/katampi/config');

        $getResponse->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'data' => ['id', 'school_id', 'cbt_url', 'api_key', 'is_active'],
            ]);

        $updateResponse = $this->actingAs($this->admin, 'sanctum')
            ->putJson('/api/v1/school/integrations/katampi/config', [
                'cbt_url' => 'http://cbt.example-school.sch.id',
                'api_key' => 'custom-production-katampi-key-99',
                'is_active' => true,
            ]);

        $updateResponse->assertStatus(200)
            ->assertJsonPath('data.cbt_url', 'http://cbt.example-school.sch.id')
            ->assertJsonPath('data.api_key', 'custom-production-katampi-key-99');
    }

    public function test_katampi_generate_sso_token(): void
    {
        $student = Student::first();

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/v1/school/integrations/katampi/sso-token', [
                'student_id' => $student?->id,
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'data' => ['token', 'redirect_url', 'expires_in', 'user'],
            ]);
    }

    public function test_katampi_get_roster(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/v1/school/integrations/katampi/roster');

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'data' => [
                    'total_students',
                    'students' => [
                        '*' => ['id', 'nisn', 'name', 'gender', 'study_group_name'],
                    ],
                ],
            ]);
    }

    public function test_katampi_sync_grades(): void
    {
        $subject = Subject::first();
        $studyGroup = StudyGroup::first();
        $student = Student::first();

        $this->assertNotNull($subject);
        $this->assertNotNull($studyGroup);
        $this->assertNotNull($student);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/v1/school/integrations/katampi/sync-grades', [
                'assessment_title' => 'Ujian Akhir Semester CBT 2026',
                'subject_id' => $subject->id,
                'study_group_id' => $studyGroup->id,
                'max_score' => 100,
                'grades' => [
                    [
                        'student_id' => $student->id,
                        'score' => 88.5,
                        'notes' => 'Diimpor dari KataMPI CBT Engine',
                    ],
                ],
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.grades_synced', 1);
    }

    public function test_whatsapp_get_stats_and_logs(): void
    {
        $statsResponse = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/v1/school/communications/whatsapp/stats');

        $statsResponse->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'data' => ['total', 'sent', 'failed', 'attendance_alerts'],
            ]);

        $logsResponse = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/v1/school/communications/whatsapp/logs');

        $logsResponse->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'data' => [
                    'data' => [
                        '*' => ['id', 'recipient_phone', 'recipient_name', 'message_type', 'content', 'status'],
                    ],
                ],
            ]);
    }

    public function test_whatsapp_send_manual_message(): void
    {
        $student = Student::first();

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/v1/school/communications/whatsapp/send', [
                'recipient_phone' => '081234567899',
                'recipient_name' => 'Wali Siswa Budi Santoso',
                'message_type' => 'announcement',
                'content' => 'Pemberitahuan gladi bersih Asesmen Nasional Berbasis Komputer.',
                'student_id' => $student?->id,
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.recipient_phone', '6281234567899')
            ->assertJsonPath('data.status', 'sent');
    }

    public function test_whatsapp_send_attendance_alerts(): void
    {
        $student = Student::first();
        $studyGroup = StudyGroup::first();

        // Ensure clean attendance record for today
        Attendance::where('student_id', $student->id)->whereDate('date', now()->toDateString())->delete();
        Attendance::create([
            'student_id' => $student->id,
            'study_group_id' => $studyGroup->id,
            'academic_year_id' => 1,
            'semester_id' => 1,
            'date' => now()->toDateString(),
            'status' => 'A',
            'notes' => 'Tanpa keterangan pada apel pagi',
            'recorded_by' => $this->admin->id,
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/v1/school/communications/whatsapp/alerts/attendance', [
                'date' => now()->toDateString(),
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'data' => ['date', 'total_detected', 'sent_count', 'skipped_count'],
            ]);
    }
}
