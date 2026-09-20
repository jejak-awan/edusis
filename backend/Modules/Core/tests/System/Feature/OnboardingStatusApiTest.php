<?php

declare(strict_types=1);

namespace Modules\Core\System\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Core\System\Models\Setting;
use Modules\School\Models\Institution\School;
use Tests\TestCase;

class OnboardingStatusApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedPermissionsAndRoles();
    }

    public function test_admin_can_fetch_onboarding_status(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/manage/system/onboarding-status');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'dismissed',
                    'steps' => ['identity', 'school_profile', 'security'],
                    'progress_percent',
                    'complete',
                ],
            ]);
    }

    public function test_dismiss_onboarding_persists_preference(): void
    {
        $admin = $this->createAdminUser();

        $this->actingAs($admin, 'sanctum')
            ->postJson('/api/v1/manage/system/onboarding/dismiss')
            ->assertOk()
            ->assertJsonPath('data.dismissed', true);

        $admin->refresh();
        $this->assertTrue((bool) $admin->getPreference('onboarding.dismissed', false));
    }

    public function test_onboarding_complete_when_school_and_2fa_exist(): void
    {
        $admin = $this->createAdminUser();
        Setting::set('app_name', 'Jejakawan EduSIS', 'string', 'system');
        Setting::set('enable_2fa', true, 'boolean', 'security');

        if (class_exists(School::class)) {
            School::create([
                'name' => 'SMKN 1 Konoha',
                'type' => 'smk',
                'npsn' => '12345678',
                'is_active' => true,
            ]);
        }

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/manage/system/onboarding-status');

        $response->assertOk()
            ->assertJsonPath('data.steps.identity', true)
            ->assertJsonPath('data.steps.school_profile', true)
            ->assertJsonPath('data.steps.security', true)
            ->assertJsonPath('data.complete', true);
    }
}
