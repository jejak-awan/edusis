<?php

declare(strict_types=1);

namespace Modules\Core\Tests\Feature;

use Illuminate\Support\Facades\Http;
use Modules\Core\System\Models\Extension;
use Modules\Core\System\Models\Setting;
use Modules\Core\System\Services\ExtensionBootstrapService;
use Modules\Core\System\Services\LicenseService;
use Modules\Core\System\Support\ExtensionFamilyCatalog;
use Modules\Layout\Models\Theme;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Feature tests for ADR-023 Phase 1: Theme Registry Packs + License Quotas.
 * Updated for ADR-032: Sarangenge as default baseline theme pack.
 */
final class ThemePackRegistryTest extends TestCase
{
    use RefreshDatabase;

    private LicenseService $license;

    protected function setUp(): void
    {
        parent::setUp();
        Setting::clearRuntimeCache();
        $this->license = app(LicenseService::class);
    }

    protected function tearDown(): void
    {
        Setting::set('theme_active', 'sarangenge');
        Setting::set('license_type', 'community');
        Setting::set('license_is_perpetual', false);
        Setting::clearRuntimeCache();

        parent::tearDown();
    }

    // -----------------------------------------------------------------------
    // ExtensionFamilyCatalog helpers
    // -----------------------------------------------------------------------

    public function test_theme_family_constant_exists(): void
    {
        $this->assertSame('theme', ExtensionFamilyCatalog::THEME);
    }

    public function test_first_party_theme_pack_slugs(): void
    {
        $slugs = ExtensionFamilyCatalog::firstPartyThemePackSlugs();

        $this->assertContains('theme-sarangenge', $slugs);
        $this->assertCount(1, $slugs);
    }

    public function test_premium_theme_pack_slugs_exclude_sarangenge(): void
    {
        $premiumSlugs = ExtensionFamilyCatalog::premiumThemePackSlugs();

        $this->assertNotContains('theme-sarangenge', $premiumSlugs);
        $this->assertCount(0, $premiumSlugs);
    }

    public function test_resolve_type_theme_maps_to_theme_family(): void
    {
        $family = ExtensionFamilyCatalog::resolve(null, 'theme-custom', 'theme', false);
        $this->assertSame(ExtensionFamilyCatalog::THEME, $family);
    }

    public function test_theme_pack_activation_priority_after_layout(): void
    {
        $layoutPriority = ExtensionFamilyCatalog::activationPriority('layout');
        $sarangengePriority = ExtensionFamilyCatalog::activationPriority('theme-sarangenge');

        $this->assertGreaterThan($layoutPriority, $sarangengePriority);
    }

    // -----------------------------------------------------------------------
    // LicenseService::getThemeQuota()
    // -----------------------------------------------------------------------

    public function test_community_quota_sarangenge_only(): void
    {
        $quota = $this->license->getThemeQuota(LicenseService::TIER_COMMUNITY);

        $this->assertSame(['sarangenge'], $quota['always_on']);
        $this->assertSame(0, $quota['max_premium_active']);
        $this->assertContains('sarangenge', $quota['catalog']);
    }

    public function test_starter_quota_same_as_community(): void
    {
        $quota = $this->license->getThemeQuota(LicenseService::TIER_STARTER);

        $this->assertSame(0, $quota['max_premium_active']);
    }

    public function test_pro_quota_allows_one_premium(): void
    {
        $quota = $this->license->getThemeQuota(LicenseService::TIER_PRO);

        $this->assertSame(1, $quota['max_premium_active']);
        $this->assertSame(['sarangenge'], $quota['always_on']);
    }

    public function test_enterprise_quota_unlimited(): void
    {
        $quota = $this->license->getThemeQuota(LicenseService::TIER_ENTERPRISE);

        $this->assertNull($quota['max_premium_active']);
    }

    public function test_white_label_quota_unlimited(): void
    {
        $quota = $this->license->getThemeQuota(LicenseService::TIER_WHITE_LABEL);

        $this->assertNull($quota['max_premium_active']);
    }

    // -----------------------------------------------------------------------
    // LicenseService::isThemeServeEntitled()
    // -----------------------------------------------------------------------

    public function test_community_can_serve_sarangenge(): void
    {
        $this->license->deactivateLicense(); // revert to community

        $this->assertTrue($this->license->isThemeServeEntitled('sarangenge'));
    }

    public function test_community_cannot_serve_premium_theme(): void
    {
        $this->license->deactivateLicense();

        $this->assertFalse($this->license->isThemeServeEntitled('custom-premium'));
    }

    public function test_pro_can_serve_sarangenge(): void
    {
        $this->license->activateLicense('JACP-PRO-TEST-ADR023-PHASE1');

        $this->assertTrue($this->license->isThemeServeEntitled('sarangenge'));
    }

    public function test_pro_can_serve_premium_when_slot_available(): void
    {
        $this->license->activateLicense('JACP-PRO-TEST-ADR023-PHASE1');
        Setting::set('license_themes_quota', [
            'always_on' => ['sarangenge'],
            'max_premium_active' => 1,
            'catalog' => ['sarangenge', 'custom-premium'],
        ], 'json');

        // No premium currently served — slot should be available
        $this->assertTrue($this->license->isThemeServeEntitled('custom-premium'));
    }

    public function test_enterprise_can_serve_all_themes(): void
    {
        $this->license->activateLicense('JACP-ENT-ADR023-TEST-9999');

        $this->assertTrue($this->license->isThemeServeEntitled('sarangenge'));
        $this->assertTrue($this->license->isThemeServeEntitled('custom-premium'));
    }

    // -----------------------------------------------------------------------
    // LicenseService::remainingPremiumSlots()
    // -----------------------------------------------------------------------

    public function test_community_remaining_slots_is_zero(): void
    {
        $this->license->deactivateLicense();

        $remaining = $this->license->remainingPremiumSlots();
        $this->assertSame(0, $remaining);
    }

    public function test_enterprise_remaining_slots_is_null_unlimited(): void
    {
        $this->license->activateLicense('JACP-ENT-ADR023-TEST-9999');

        $remaining = $this->license->remainingPremiumSlots();
        $this->assertNull($remaining);
    }

    // -----------------------------------------------------------------------
    // features_matrix backward compat
    // -----------------------------------------------------------------------

    public function test_features_matrix_includes_theme_quota_key(): void
    {
        $matrix = $this->license->getFeaturesMatrix(LicenseService::TIER_PRO);

        $this->assertArrayHasKey('theme_quota', $matrix);
        $this->assertArrayHasKey('premium_themes', $matrix);   // deprecated alias still present
        $this->assertIsArray($matrix['theme_quota']);
        $this->assertArrayHasKey('always_on', $matrix['theme_quota']);
        $this->assertArrayHasKey('max_premium_active', $matrix['theme_quota']);
        $this->assertArrayHasKey('catalog', $matrix['theme_quota']);
    }

    // -----------------------------------------------------------------------
    // ExtensionBootstrapService discovery
    // -----------------------------------------------------------------------

    public function test_theme_pack_discovery_creates_registry_rows(): void
    {
        $bootstrapService = app(ExtensionBootstrapService::class);
        $bootstrapService->discover();

        foreach (ExtensionFamilyCatalog::firstPartyThemePackSlugs() as $packSlug) {
            $ext = Extension::query()->where('slug', $packSlug)->first();
            $this->assertNotNull($ext, "Expected sys_extensions row for [{$packSlug}] after discover().");
            $this->assertSame('theme', $ext->type, "Expected type=theme for [{$packSlug}].");
            $this->assertSame(ExtensionFamilyCatalog::THEME, $ext->family, "Expected family=theme for [{$packSlug}].");
        }
    }

    public function test_theme_sarangenge_pack_manifest_column_has_theme_flags(): void
    {
        $bootstrapService = app(ExtensionBootstrapService::class);
        $bootstrapService->discover();

        $sarangengePack = Extension::query()->where('slug', 'theme-sarangenge')->first();
        $this->assertNotNull($sarangengePack);

        $manifest = $sarangengePack->manifest;
        $this->assertIsArray($manifest);
        $this->assertArrayHasKey('theme_flags', $manifest);
        $this->assertTrue((bool) ($manifest['theme_flags']['always_on'] ?? false));
        $this->assertTrue((bool) ($manifest['theme_flags']['non_deactivatable'] ?? false));
        $this->assertFalse((bool) ($manifest['theme_flags']['premium'] ?? true));
    }

    public function test_theme_slug_for_pack_resolves_theme_slug(): void
    {
        $this->assertSame('sarangenge', ExtensionFamilyCatalog::themeSlugForPack('theme-sarangenge'));
        $this->assertNull(ExtensionFamilyCatalog::themeSlugForPack('cms-publishing'));
    }

    public function test_is_premium_theme_pack_slug_identifies_premium_packs(): void
    {
        $this->assertFalse(ExtensionFamilyCatalog::isPremiumThemePackSlug('theme-sarangenge'));
        $this->assertFalse(ExtensionFamilyCatalog::isPremiumThemePackSlug('layout'));
    }

    public function test_extension_controller_attaches_is_served_to_theme_packs(): void
    {
        $this->seedPermissionsAndRoles();
        $admin = $this->createAdminUser();

        $bootstrapService = app(ExtensionBootstrapService::class);
        $bootstrapService->discover();

        Setting::set('theme_active', 'sarangenge');

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/manage/infra/extensions');

        $response->assertOk();
        $data = collect($response->json('data'));

        $sarangenge = $data->firstWhere('slug', 'theme-sarangenge');
        $this->assertNotNull($sarangenge);
        $this->assertTrue((bool) ($sarangenge['is_served'] ?? false));
    }

    public function test_theme_controller_returns_3level_meta_and_quota(): void
    {
        $this->seedPermissionsAndRoles();
        $admin = $this->createAdminUser();

        // Ensure Layout module is active
        Extension::updateOrCreate(
            ['slug' => 'layout'],
            ['type' => 'module', 'name' => 'Layout', 'version' => '1.0.0', 'status' => 'active', 'is_core' => false]
        );

        $response = $this->actingAs($admin, 'sanctum')
            ->getJson('/api/v1/manage/layout/themes');

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'message',
                'data',
                'meta' => [
                    'quota' => [
                        'tier',
                        'tier_label',
                        'max_premium_active',
                        'remaining_slots',
                        'is_unlimited',
                    ],
                    'site_active',
                ],
            ]);

        $themes = collect($response->json('data'));
        $this->assertNotEmpty($themes);

        // Every theme item must have 3-level & quota properties
        $themes->each(function (array $item): void {
            $this->assertArrayHasKey('is_pack_enabled', $item);
            $this->assertArrayHasKey('is_entitled', $item);
            $this->assertArrayHasKey('is_premium', $item);
        });
    }

    // -----------------------------------------------------------------------
    // ADR-023 Phase 2: JA-CP themes.* payload integration & heartbeat
    // -----------------------------------------------------------------------

    public function test_get_license_status_includes_top_level_theme_quota(): void
    {
        $status = $this->license->getLicenseStatus();

        $this->assertArrayHasKey('theme_quota', $status);
        $this->assertIsArray($status['theme_quota']);
        $this->assertArrayHasKey('always_on', $status['theme_quota']);
        $this->assertArrayHasKey('max_premium_active', $status['theme_quota']);
        $this->assertArrayHasKey('catalog', $status['theme_quota']);
    }

    public function test_jacp_activate_persists_custom_themes_quota(): void
    {
        Http::fake([
            'https://cp.jejakawan.com/api/v1/licenses/activate' => Http::response([
                'valid' => true,
                'data' => [
                    'tier' => 'pro',
                    'expires_at' => null,
                    'themes' => [
                        'always_on' => ['sarangenge'],
                        'max_premium_active' => 2,
                        'catalog' => ['sarangenge', 'custom-premium'],
                    ],
                ],
            ], 200),
        ]);

        $res = $this->license->activateLicense('JACP-PRO-CUSTOM-REMOTE-KEY');
        $this->assertTrue($res['success']);

        $quota = $this->license->getThemeQuota();
        $this->assertSame(2, $quota['max_premium_active']);
        $this->assertSame(['sarangenge'], $quota['always_on']);
        $this->assertSame(['sarangenge', 'custom-premium'], $quota['catalog']);

        // Entitlement checks
        $this->assertTrue($this->license->isThemeServeEntitled('sarangenge'));
        $this->assertTrue($this->license->isThemeServeEntitled('custom-premium'));
        $this->assertFalse($this->license->isThemeServeEntitled('unentitled-theme'));
    }

    public function test_jacp_payload_partial_missing_fields_falls_back_safely(): void
    {
        Http::fake([
            'https://cp.jejakawan.com/api/v1/licenses/activate' => Http::response([
                'valid' => true,
                'data' => [
                    'tier' => 'pro',
                    'expires_at' => null,
                    'themes' => [
                        'max_premium_active' => 3,
                    ],
                ],
            ], 200),
        ]);

        $res = $this->license->activateLicense('JACP-PRO-PARTIAL-KEY');
        $this->assertTrue($res['success']);

        $quota = $this->license->getThemeQuota();
        $this->assertSame(3, $quota['max_premium_active']);
        $this->assertSame(['sarangenge'], $quota['always_on']);
        $this->assertContains('sarangenge', $quota['catalog']);
    }

    public function test_jacp_heartbeat_sync_updates_theme_quota_and_remediates(): void
    {
        Setting::set('license_is_perpetual', false);
        // Activate Pro
        $this->license->activateLicense('JACP-PRO-HEARTBEAT-TEST');
        Setting::set('license_key', 'JACP-PRO-HEARTBEAT-TEST');

        // Ensure sarangenge and custom theme exist in lay_themes
        Theme::updateOrCreate(
            ['slug' => 'sarangenge'],
            ['name' => 'Sarangenge', 'type' => 'frontend', 'path' => 'themes/sarangenge', 'is_active' => false, 'status' => 'inactive']
        );
        Theme::updateOrCreate(
            ['slug' => 'custom-premium'],
            ['name' => 'Custom Premium', 'type' => 'frontend', 'path' => 'themes/custom-premium', 'is_active' => true, 'status' => 'active']
        );

        // Set custom-premium active
        Theme::query()->where('type', 'frontend')->update(['is_active' => false]);
        Theme::query()->where('slug', 'custom-premium')->update(['is_active' => true]);
        Setting::set('theme_active', 'custom-premium');

        // Mock heartbeat response that reduces quota to 0 premium themes
        Http::fake([
            'https://cp.jejakawan.com/api/v1/licenses/heartbeat' => Http::response([
                'valid' => true,
                'data' => [
                    'tier' => 'community',
                    'themes' => [
                        'always_on' => ['sarangenge'],
                        'max_premium_active' => 0,
                        'catalog' => ['sarangenge'],
                    ],
                ],
            ], 200),
        ]);

        $res = $this->license->syncHeartbeat(true);
        $this->assertTrue($res['success']);

        // custom-premium should have been remediated back to sarangenge
        $activeTheme = Theme::query()->where('type', 'frontend')->where('is_active', true)->first();
        $this->assertNotNull($activeTheme);
        $this->assertSame('sarangenge', $activeTheme->slug);
        $this->assertSame('sarangenge', Setting::get('theme_active'));
    }

    public function test_deactivate_license_clears_themes_quota_setting(): void
    {
        Setting::set('license_themes_quota', [
            'always_on' => ['sarangenge'],
            'max_premium_active' => 5,
            'catalog' => ['sarangenge', 'custom-theme'],
        ], 'json');

        $this->license->deactivateLicense();

        $this->assertNull(Setting::get('license_themes_quota'));
        $quota = $this->license->getThemeQuota();
        $this->assertSame(0, $quota['max_premium_active']);
    }
}
