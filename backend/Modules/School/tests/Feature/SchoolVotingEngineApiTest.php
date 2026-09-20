<?php

declare(strict_types=1);

namespace Modules\School\Tests\Feature;

use Carbon\Carbon;
use Modules\Core\System\Models\User;
use Modules\School\Database\Seeders\SchoolPhase1Seeder;
use Modules\School\Database\Seeders\SchoolPhase2Seeder;
use Modules\School\Database\Seeders\SchoolPhase3Seeder;
use Modules\School\Database\Seeders\SchoolPhase4Seeder;
use Modules\School\Database\Seeders\SchoolVotingEngineSeeder;
use Modules\School\Models\Institution\School;
use Modules\School\Models\Voting\VoteEvent;
use Modules\School\Models\Voting\VoteVoter;
use Tests\TestCase;

class SchoolVotingEngineApiTest extends TestCase
{
    private User $admin;

    private School $school;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(SchoolPhase1Seeder::class);
        $this->seed(SchoolPhase2Seeder::class);
        $this->seed(SchoolPhase3Seeder::class);
        $this->seed(SchoolPhase4Seeder::class);
        $this->seed(SchoolVotingEngineSeeder::class);

        $this->admin = User::firstOrCreate(
            ['email' => 'admin@edusis.local'],
            [
                'name' => 'Super Administrator',
                'password' => bcrypt('password'),
            ]
        );

        $this->school = School::first();
    }

    public function test_get_voting_events_list(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/v1/school/voting/events');

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'type',
                        'voting_mode',
                        'target_audience',
                        'start_date',
                        'end_date',
                        'status',
                    ],
                ],
            ]);
    }

    public function test_store_voting_event(): void
    {
        $payload = [
            'title' => 'Pemilihan Ketua Ekskul Robotik 2026',
            'type' => 'ekskul',
            'voting_mode' => 'token_kiosk',
            'target_audience' => 'all_students',
            'start_date' => Carbon::now()->toIso8601String(),
            'end_date' => Carbon::now()->addDays(3)->toIso8601String(),
            'status' => 'active',
            'allow_abstain' => false,
            'description' => 'Pemilihan ketua klub robotik dan IoT SMKN 6 Bandung.',
        ];

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/v1/school/voting/events', $payload);

        $response->assertStatus(201)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.title', 'Pemilihan Ketua Ekskul Robotik 2026')
            ->assertJsonPath('data.type', 'ekskul');
    }

    public function test_show_voting_event_detail(): void
    {
        $event = VoteEvent::where('school_id', $this->school->id)->first();
        $this->assertNotNull($event);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/v1/school/voting/events/{$event->id}");

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.id', $event->id)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'candidates',
                    'voters_count',
                    'ballots_count',
                ],
            ]);
    }

    public function test_add_candidate_to_event(): void
    {
        $event = VoteEvent::where('school_id', $this->school->id)->first();
        $this->assertNotNull($event);

        $payload = [
            'candidate_number' => 99,
            'title' => 'Kandidat Alternatif Inovasi',
            'vision' => 'Mewujudkan sekolah berbasis teknologi terdepan',
            'mission' => 'Pelatihan AI dan otomatisasi untuk seluruh siswa',
        ];

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson("/api/v1/school/voting/events/{$event->id}/candidates", $payload);

        $response->assertStatus(201)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.candidate_number', 99)
            ->assertJsonPath('data.title', 'Kandidat Alternatif Inovasi');
    }

    public function test_generate_voters_batch_tokens(): void
    {
        $newEvent = VoteEvent::create([
            'school_id' => $this->school->id,
            'title' => 'Polling Fasilitas Lab Komputer',
            'type' => 'polling',
            'voting_mode' => 'token_kiosk',
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addDays(2),
            'status' => 'active',
        ]);

        $payload = [
            'source' => 'all_students',
        ];

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson("/api/v1/school/voting/events/{$newEvent->id}/generate-voters", $payload);

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'data' => [
                    'event_id',
                    'generated_count',
                    'total_dpt',
                ],
            ]);
    }

    public function test_get_voters_list(): void
    {
        $event = VoteEvent::where('school_id', $this->school->id)->first();
        $this->assertNotNull($event);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/v1/school/voting/events/{$event->id}/voters");

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'data' => [
                    'data' => [
                        '*' => [
                            'id',
                            'event_id',
                            'voter_name',
                            'token',
                            'has_voted',
                        ],
                    ],
                ],
            ]);
    }

    public function test_cast_vote_via_kiosk_token_and_anti_double_voting(): void
    {
        $event = VoteEvent::where('school_id', $this->school->id)->where('status', 'active')->first();
        $this->assertNotNull($event);

        $candidate = $event->candidates()->first();
        $this->assertNotNull($candidate);

        $initialCount = $candidate->vote_count;

        // Buat pemilih khusus belum memilih
        $token = VoteVoter::generateUniqueToken($event->id);
        $voter = VoteVoter::create([
            'event_id' => $event->id,
            'voter_type' => 'general',
            'voter_name' => 'Pemilih Uji Coba Kiosk',
            'token' => $token,
            'has_voted' => false,
            'token_generated_at' => Carbon::now(),
        ]);

        // 1. Coblos pertama kali dengan token
        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson("/api/v1/school/voting/events/{$event->id}/vote", [
                'candidate_id' => $candidate->id,
                'token' => $token,
                'mode' => 'kiosk',
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.voted', true)
            ->assertJsonStructure([
                'data' => [
                    'voted',
                    'receipt_code',
                    'candidate_name',
                    'voted_at',
                ],
            ]);

        // Verifikasi kandidat bertambah suaranya
        $candidate->refresh();
        $this->assertEquals($initialCount + 1, $candidate->vote_count);

        // Verifikasi voter bertanda sudah memilih
        $voter->refresh();
        $this->assertTrue($voter->has_voted);

        // Verifikasi kotak suara rahasia (Secret Ballot) tercatat
        $this->assertDatabaseHas('sch_vote_ballots', [
            'event_id' => $event->id,
            'candidate_id' => $candidate->id,
            'vote_mode' => 'kiosk',
        ]);

        // 2. Coblos KEDUA KALI dengan token yang sama -> HARUS DITOLAK (Anti Double Voting)
        $rejectResponse = $this->actingAs($this->admin, 'sanctum')
            ->postJson("/api/v1/school/voting/events/{$event->id}/vote", [
                'candidate_id' => $candidate->id,
                'token' => $token,
                'mode' => 'kiosk',
            ]);

        $rejectResponse->assertStatus(422)
            ->assertJson(['success' => false]);
    }

    public function test_quick_count_realtime_metrics(): void
    {
        $event = VoteEvent::where('school_id', $this->school->id)->first();
        $this->assertNotNull($event);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/v1/school/voting/events/{$event->id}/quick-count");

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'data' => [
                    'event_id',
                    'event_title',
                    'event_type',
                    'status',
                    'total_ballots',
                    'total_dpt',
                    'voted_dpt',
                    'turnout_percentage',
                    'candidates' => [
                        '*' => [
                            'id',
                            'candidate_number',
                            'title',
                            'vote_count',
                            'percentage',
                        ],
                    ],
                ],
            ]);
    }
}
