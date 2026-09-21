<?php

declare(strict_types=1);

namespace Modules\School\Tests\Feature;

use Modules\Core\System\Models\User;
use Modules\School\Database\Seeders\SchoolPhase1Seeder;
use Modules\School\Database\Seeders\SchoolPhase2Seeder;
use Modules\School\Database\Seeders\SchoolPhase3Seeder;
use Modules\School\Database\Seeders\SchoolPhase4Seeder;
use Modules\School\Models\Academic\AcademicYear;
use Modules\School\Models\Academic\Department;
use Modules\School\Models\HR\JobApplication;
use Modules\School\Models\HR\JobVacancy;
use Modules\School\Models\Institution\School;
use Modules\School\Models\Student\Alumni;
use Modules\School\Models\Student\GraduationBatch;
use Modules\School\Models\Student\Student;
use Tests\TestCase;

class SchoolAlumniAndCareerApiTest extends TestCase
{
    private User $admin;

    private School $school;

    private Student $student;

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

        $this->school = School::first();
        $this->student = Student::first();
    }

    public function test_get_and_store_graduation_batch(): void
    {
        $year = AcademicYear::first();

        // 1. Store Batch
        $createResponse = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/v1/school/graduation/batches', [
                'academic_year_id' => $year?->id,
                'batch_year' => '2026',
                'batch_name' => 'Angkatan ke-25 (Tahun 2026)',
                'graduation_date' => '2026-05-15',
                'decree_number' => '421.5/SK-LULUS/2026',
                'notes' => 'Kelulusan Kurikulum Merdeka Terpadu',
            ]);

        $createResponse->assertStatus(201)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.batch_year', '2026');

        $batchId = $createResponse->json('data.id');

        // 2. Get Batches
        $listResponse = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/v1/school/graduation/batches');

        $listResponse->assertStatus(200)
            ->assertJson(['success' => true]);

        // 3. Show Batch
        $showResponse = $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/v1/school/graduation/batches/{$batchId}");

        $showResponse->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.id', $batchId);
    }

    public function test_process_mass_graduation_and_skl(): void
    {
        $batch = GraduationBatch::create([
            'school_id' => $this->school->id,
            'batch_year' => '2026',
            'batch_name' => 'Angkatan 2026',
            'graduation_date' => '2026-05-15',
            'decree_number' => '421/001/SKL-2026',
            'total_students' => 0,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/v1/school/graduation/process', [
                'graduation_batch_id' => $batch->id,
                'student_ids' => [$this->student->id],
            ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.processed_students_count', 1);

        // Verify student status changed
        $this->student->refresh();
        $this->assertEquals('graduated', $this->student->status);

        // Verify alumni created
        $alumni = Alumni::where('student_id', $this->student->id)->first();
        $this->assertNotNull($alumni);
        $this->assertEquals('2026', $alumni->graduation_year);

        // Verify SKL endpoint
        $sklResponse = $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/v1/school/graduation/students/{$this->student->id}/skl");

        $sklResponse->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'data' => [
                    'student',
                    'batch',
                    'skl_number',
                    'graduation_year',
                    'verification_hash',
                ],
            ]);
    }

    public function test_alumni_directory_and_tracer_update(): void
    {
        $alumni = Alumni::firstOrCreate(
            [
                'school_id' => $this->school->id,
                'student_id' => $this->student->id,
            ],
            [
                'graduation_year' => '2026',
                'career_status' => 'mencari_kerja',
                'is_public' => true,
            ]
        );

        // 1. List Alumni
        $listResponse = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/v1/school/alumni');

        $listResponse->assertStatus(200)
            ->assertJson(['success' => true]);

        // 2. Update Tracer Study
        $updateResponse = $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/v1/school/alumni/{$alumni->id}", [
                'career_status' => 'kerja',
                'company_or_campus' => 'PT Teknologi Edukasi Nusantara',
                'current_position' => 'Junior Frontend Developer',
                'salary_range' => 'Rp 5.000.000 - Rp 8.000.000',
                'contact_email' => 'alumni.dev@gmail.com',
                'linkedin_url' => 'https://linkedin.com/in/alumni-dev',
            ]);

        $updateResponse->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.career_status', 'kerja')
            ->assertJsonPath('data.company_or_campus', 'PT Teknologi Edukasi Nusantara');

        // 3. Tracer Stats
        $statsResponse = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/v1/school/alumni/stats/tracer');

        $statsResponse->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure([
                'data' => [
                    'total_alumni',
                    'absorbed_count',
                    'absorption_rate',
                    'breakdown' => [
                        'kerja',
                        'kuliah',
                        'wirausaha',
                        'mencari_kerja',
                        'lainnya',
                    ],
                ],
            ]);
    }

    public function test_bkk_job_vacancies_and_application_workflow(): void
    {
        $dept = Department::first();

        // 1. Store Vacancy
        $createResponse = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/v1/school/career/vacancies', [
                'department_id' => $dept?->id,
                'company_name' => 'PT Inovasi Digital Nusantara',
                'job_title' => 'Junior Web Engineer',
                'job_type' => 'fulltime',
                'workplace_type' => 'hybrid',
                'location' => 'Bandung, Jawa Barat',
                'salary_range' => 'Rp 6.000.000 - Rp 9.000.000',
                'description' => 'Membangun dashboard antarmuka Vue 3 & integrasi RESTful API Laravel.',
                'requirements' => 'Menguasai TypeScript, Vue 3 Composition API, dan Tailwind CSS.',
                'application_deadline' => '2026-12-31',
                'contact_email' => 'hr@inovasidigital.id',
                'status' => 'published',
            ]);

        $createResponse->assertStatus(201)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.company_name', 'PT Inovasi Digital Nusantara');

        $vacancyId = $createResponse->json('data.id');

        // 2. Get Vacancies List
        $listResponse = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/v1/school/career/vacancies');

        $listResponse->assertStatus(200)
            ->assertJson(['success' => true]);

        // 3. Apply for Vacancy
        $applyResponse = $this->actingAs($this->admin, 'sanctum')
            ->postJson("/api/v1/school/career/vacancies/{$vacancyId}/apply", [
                'student_id' => $this->student->id,
                'applicant_name' => $this->student->full_name,
                'applicant_email' => 'siswa.pelamar@sekolah.id',
                'applicant_phone' => '081234567899',
                'resume_url' => 'https://storage.sekolah.id/resumes/raditya.pdf',
                'portfolio_url' => 'https://github.com/raditya-dev',
                'cover_letter' => 'Saya tertarik melamar sebagai Junior Web Engineer.',
            ]);

        $applyResponse->assertStatus(201)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.status', 'submitted');

        $applicationId = $applyResponse->json('data.id');

        // 4. Get Vacancy Applications
        $appsResponse = $this->actingAs($this->admin, 'sanctum')
            ->getJson("/api/v1/school/career/vacancies/{$vacancyId}/applications");

        $appsResponse->assertStatus(200)
            ->assertJson(['success' => true]);
        $this->assertCount(1, $appsResponse->json('data'));

        // 5. Update Application Status
        $statusResponse = $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/v1/school/career/applications/{$applicationId}/status", [
                'status' => 'shortlisted',
                'notes' => 'Kandidat memenuhi kualifikasi portofolio Vue 3.',
            ]);

        $statusResponse->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.status', 'shortlisted');

        // 6. Career Stats
        $statsResponse = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/v1/school/career/stats');

        $statsResponse->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.total_vacancies', 1)
            ->assertJsonPath('data.total_applications', 1);
    }
}
