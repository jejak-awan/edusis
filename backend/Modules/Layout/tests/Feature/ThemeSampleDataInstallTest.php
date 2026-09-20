<?php

declare(strict_types=1);

namespace Modules\Layout\Tests\Feature;

use Modules\Layout\Models\Menu;
use Modules\Layout\Models\Theme;
use Modules\Layout\SampleData\ThemeSampleDataInstallOptions;
use Modules\Layout\SampleData\ThemeSampleDataOrchestrator;
use Modules\Publishing\Models\Content;
use Tests\TestCase;

class ThemeSampleDataInstallTest extends TestCase
{
    public function test_install_sample_data_is_idempotent_for_sarangenge(): void
    {
        $this->activatePack('layout');
        $this->activatePack('publishing');
        $this->seedPermissionsAndRoles();
        $this->createSuperAdminUser();

        Theme::query()->delete();

        $theme = Theme::factory()->create([
            'name' => 'Sarangenge',
            'slug' => 'sarangenge',
            'type' => 'frontend',
            'status' => 'active',
            'is_active' => true,
            'settings' => [],
        ]);

        $orchestrator = app(ThemeSampleDataOrchestrator::class);
        $options = new ThemeSampleDataInstallOptions(force: true);

        $first = $orchestrator->install($theme, $options);
        $second = $orchestrator->install($theme->fresh(), $options);

        $this->assertGreaterThan(0, $first->menusInstalled);
        $this->assertGreaterThan(0, $first->pagesInstalled);
        $this->assertGreaterThan(0, $first->settingsApplied);

        $this->assertSame($first->menusInstalled, $second->menusInstalled);
        $this->assertSame($first->pagesInstalled, $second->pagesInstalled);

        $headerMenu = Menu::query()->where('slug', 'sarangenge-sample-header')->first();
        $this->assertNotNull($headerMenu);
        $this->assertGreaterThan(0, $headerMenu->parentItems()->count());

        $headerUrls = $headerMenu->items()->pluck('url')->all();
        $this->assertContains('/about', $headerUrls);
        $this->assertContains('/achievement', $headerUrls);
        $this->assertContains('/facilities', $headerUrls);

        $profil = $headerMenu->items()->where('url', '/about')->whereNull('parent_id')->first();
        $this->assertNotNull($profil);
        $childUrls = $headerMenu->items()->where('parent_id', $profil->id)->pluck('url')->all();
        $this->assertSame(['/about#profil', '/team', '/achievement', '/facilities'], array_values($childUrls));

        $theme->refresh();
        $this->assertSame((string) $headerMenu->id, $theme->settings['menu_location_header'] ?? null);

        $homePage = Content::query()->where('slug', 'home')->where('type', 'page')->first();
        $this->assertNotNull($homePage);
        $this->assertSame('published', $homePage->status);
        $this->assertSame('pages/Home', $homePage->meta['theme_page'] ?? null);
        $this->assertSame('sarangenge', $homePage->meta['sample_theme'] ?? null);
        $this->assertTrue($homePage->meta['use_theme_template'] ?? false);
        $this->assertFalse($homePage->meta['builder_override'] ?? true);
        $this->assertSame([], $homePage->meta['builder_blocks'] ?? ['not-empty']);
    }

    public function test_install_sample_via_api(): void
    {
        $this->activatePack('layout');
        $this->activatePack('publishing');
        $this->seedPermissionsAndRoles();

        Theme::query()->delete();

        Theme::factory()->create([
            'name' => 'Sarangenge',
            'slug' => 'sarangenge',
            'type' => 'frontend',
            'status' => 'active',
            'is_active' => true,
            'settings' => [],
        ]);

        $user = $this->createSuperAdminUser();

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/manage/layout/themes/sarangenge/install-sample', [
                'force' => true,
            ])
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'theme_slug',
                    'menus_installed',
                    'pages_installed',
                    'settings_applied',
                    'messages',
                    'warnings',
                ],
            ]);

        $this->assertNotNull(Menu::query()->where('slug', 'sarangenge-sample-header')->first());

        $homePage = Content::query()->where('slug', 'home')->where('type', 'page')->first();
        $this->assertNotNull($homePage);
        $this->assertTrue($homePage->meta['use_theme_template'] ?? false);
        $this->assertFalse($homePage->meta['builder_override'] ?? true);
        $this->assertSame([], $homePage->meta['builder_blocks'] ?? ['not-empty']);
    }
}
