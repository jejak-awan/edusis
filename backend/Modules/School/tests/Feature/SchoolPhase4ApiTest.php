<?php

declare(strict_types=1);

namespace Modules\School\Tests\Feature;

use Modules\Core\System\Models\User;
use Modules\School\Database\Seeders\SchoolPhase1Seeder;
use Modules\School\Database\Seeders\SchoolPhase2Seeder;
use Modules\School\Database\Seeders\SchoolPhase3Seeder;
use Modules\School\Database\Seeders\SchoolPhase4Seeder;
use Modules\School\Models\Academic\Department;
use Modules\School\Models\Administration\PpdbRegistration;
use Modules\School\Models\Institution\School;
use Modules\School\Models\Student\Student;
use Modules\School\Models\StudentAffairs\OsisCandidate;
use Modules\School\Models\StudentAffairs\OsisElection;
use Modules\School\Models\StudentAffairs\OsisVote;
use Tests\TestCase;

class SchoolPhase4ApiTest extends TestCase
{
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SchoolPhase1Seeder::class);
        $this->seed(SchoolPhase2Seeder::class);
        $this->seed(SchoolPhase3Seeder::class);
        $this->seed(SchoolPhase4Seeder::class);

        $this->admin = User::firstOrCreate(
            ['email' => 'admin@edusis.local'],
            [
                'name' => 'Super Administrator',
                'password' => bcrypt('password'),
            ]
        );
    }

    public function test_get_letters_list(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/v1/school/letters');

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'data' => [
                    'data' => [
                        '*' => ['id', 'letter_number', 'type', 'category', 'sender', 'recipient', 'subject'],
                    ],
                ],
            ]);
    }

    public function test_generate_suggested_letter_number(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/v1/school/letters/generate-number?type=outgoing&category=invitation');

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'data' => ['suggested_number'],
            ]);

        $suggested = $response->json('data.suggested_number');
        $this->assertStringContainsString('TU-OUT', $suggested);
        $this->assertStringContainsString('005', $suggested);
    }

    public function test_create_and_update_disposition_letter(): void
    {
        $payload = [
            'type' => 'incoming',
            'letter_number' => 'TEST-IN/'.uniqid(),
            'category' => 'general',
            'sender' => 'Kantor Kemenag Wilayah',
            'recipient' => 'Kepala Sekolah',
            'subject' => 'Surat Edaran Pembinaan Rohani',
            'letter_date' => now()->toDateString(),
            'received_or_sent_date' => now()->toDateString(),
            'status' => 'registered',
        ];

        $res = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/v1/school/letters', $payload);

        $res->assertStatus(201)
            ->assertJson(['success' => true]);

        $letterId = $res->json('data.id');

        // Update disposition
        $dispRes = $this->actingAs($this->admin, 'sanctum')
            ->postJson("/api/v1/school/letters/{$letterId}/disposition", [
                'disposition_to' => 'Guru PAI & Pembina Rohis',
                'disposition_instruction' => 'Harap koordinasikan dengan ketua ekstrakurikuler Rohis.',
                'disposition_status' => 'processed',
            ]);

        $dispRes->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.disposition_status', 'processed')
            ->assertJsonPath('data.disposition_to', 'Guru PAI & Pembina Rohis');
    }

    public function test_get_ppdb_list(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/v1/school/ppdb');

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'data' => [
                    'data' => [
                        '*' => ['id', 'registration_number', 'full_name', 'track', 'status'],
                    ],
                ],
            ]);
    }

    public function test_store_and_verify_ppdb_applicant(): void
    {
        $dept = Department::first();

        $payload = [
            'department_id' => $dept->id,
            'track' => 'prestasi_akademik',
            'full_name' => 'Fajar Nugraha',
            'nisn' => '0098765432',
            'nik' => '3273010101080009',
            'gender' => 'L',
            'phone' => '081299998888',
            'previous_school' => 'SMP Pasundan 1 Bandung',
            'average_report_score' => 86.5,
        ];

        $res = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/v1/school/ppdb', $payload);

        $res->assertStatus(201)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.status', 'submitted');

        $ppdbId = $res->json('data.id');

        // Verify applicant
        $verifyRes = $this->actingAs($this->admin, 'sanctum')
            ->postJson("/api/v1/school/ppdb/{$ppdbId}/verify", [
                'status' => 'verified',
                'notes' => 'Berkas dan nilai rapor valid.',
            ]);

        $verifyRes->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.status', 'verified');
    }

    public function test_admit_ppdb_applicant_as_student(): void
    {
        $applicant = PpdbRegistration::where('status', '!=', 'accepted')->first();
        if (! $applicant) {
            $dept = Department::first();
            $applicant = PpdbRegistration::create([
                'school_id' => School::first()->id,
                'academic_year_id' => 1,
                'department_id' => $dept->id,
                'registration_number' => 'PPDB/TEST/'.uniqid(),
                'track' => 'zonasi',
                'full_name' => 'Budi Santoso',
                'gender' => 'L',
                'status' => 'verified',
            ]);
        }

        $res = $this->actingAs($this->admin, 'sanctum')
            ->postJson("/api/v1/school/ppdb/{$applicant->id}/admit");

        $res->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'data' => [
                    'registration',
                    'student' => ['id', 'full_name', 'nis'],
                ],
            ]);

        $this->assertEquals('accepted', $res->json('data.registration.status'));
    }

    public function test_guest_book_checkin_and_checkout(): void
    {
        $payload = [
            'visitor_name' => 'Prof. Hendra Kusuma',
            'institution' => 'Institut Teknologi Bandung',
            'phone' => '081233445566',
            'identity_type' => 'KTP',
            'identity_number' => '3273010101750005',
            'purpose' => 'Riset Pembelajaran Vokasi Berbasis AI',
            'target_person' => 'Kepala Sekolah & Guru Kejuruan',
        ];

        $res = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/v1/school/guest-book', $payload);

        $res->assertStatus(201)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.status', 'checked_in');

        $guestId = $res->json('data.id');
        $this->assertNotEmpty($res->json('data.badge_number'));

        // Checkout
        $outRes = $this->actingAs($this->admin, 'sanctum')
            ->postJson("/api/v1/school/guest-book/{$guestId}/checkout", [
                'notes' => 'Kunjungan selesai dengan baik.',
            ]);

        $outRes->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.status', 'checked_out');
    }

    public function test_guest_and_ppdb_alias_routes(): void
    {
        // PPDB alias route /school/ppdb/applicants
        $ppdbRes = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/v1/school/ppdb/applicants');
        $ppdbRes->assertStatus(200)
            ->assertJson(['success' => true]);

        // Guest book alias route /school/guests
        $guestRes = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/v1/school/guests');
        $guestRes->assertStatus(200)
            ->assertJson(['success' => true]);
    }

    public function test_osis_elections_and_quick_count(): void
    {
        $res = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/v1/school/osis/elections');

        $res->assertStatus(200)
            ->assertJson(['success' => true]);

        $election = OsisElection::where('status', 'active')->first();
        if ($election) {
            $qcRes = $this->actingAs($this->admin, 'sanctum')
                ->getJson("/api/v1/school/osis/elections/{$election->id}/quick-count");

            $qcRes->assertStatus(200)
                ->assertJson(['success' => true])
                ->assertJsonStructure([
                    'data' => [
                        'election_title',
                        'period',
                        'total_votes',
                        'candidates',
                    ],
                ]);
        }
    }

    public function test_osis_prevent_double_voting(): void
    {
        $election = OsisElection::where('status', 'active')->first();
        $this->assertNotNull($election, 'Active OSIS election should exist from seeder');

        $candidate = OsisCandidate::where('election_id', $election->id)->first();
        $this->assertNotNull($candidate, 'Candidate should exist for election');

        $student = Student::first();
        $this->assertNotNull($student, 'Student should exist for voting');

        // First vote (clear previous test vote if any)
        OsisVote::where('election_id', $election->id)
            ->where('voter_student_id', $student->id)
            ->delete();

        $voteRes = $this->actingAs($this->admin, 'sanctum')
            ->postJson("/api/v1/school/osis/elections/{$election->id}/vote", [
                'candidate_id' => $candidate->id,
                'voter_student_id' => $student->id,
                'token' => 'test-voting-token',
            ]);

        $voteRes->assertStatus(200)
            ->assertJson(['success' => true]);

        // Attempt duplicate vote
        $dupRes = $this->actingAs($this->admin, 'sanctum')
            ->postJson("/api/v1/school/osis/elections/{$election->id}/vote", [
                'candidate_id' => $candidate->id,
                'voter_student_id' => $student->id,
            ]);

        $dupRes->assertStatus(422)
            ->assertJson(['success' => false]);
    }

    public function test_get_extracurriculars_list(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/v1/school/extracurriculars');

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'category', 'schedule_day'],
                ],
            ]);
    }
}
