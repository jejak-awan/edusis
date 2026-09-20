<?php

declare(strict_types=1);

namespace Modules\Layout\Console\Commands;

use Illuminate\Console\Command;
use Modules\Core\System\Models\Setting;
use Modules\Layout\Database\Seeders\Themes\SarangengeThemeDemoSeeder;
use Modules\Layout\Services\ThemeService;

class ThemeSeedCommand extends Command
{
    protected $signature = 'theme:seed
                            {slug? : Theme slug (sarangenge). Defaults to active theme.}
                            {--all : Seed demo data for all supported themes}
                            {--force : Force overwrite existing theme settings, identity, and sample data}';

    protected $description = 'Seed generic starter demo data for a theme (sarangenge) or the currently active theme.';

    public function handle(ThemeService $themeService): int
    {
        $force = (bool) $this->option('force');
        config(['layout.theme_seed_force' => $force]);

        $themeService->scanThemes();

        if ($this->option('all')) {
            $this->info('Seeding demo data for all official themes...');
            $this->call('db:seed', ['--class' => SarangengeThemeDemoSeeder::class]);
            $this->info('Theme demo dataset seeding completed.');

            return self::SUCCESS;
        }

        $slugArg = $this->argument('slug');
        $slugRaw = is_string($slugArg) && $slugArg !== '' ? $slugArg : Setting::get('theme_active', 'sarangenge');
        $slug = strtolower(trim(is_string($slugRaw) ? $slugRaw : 'sarangenge'));

        $this->info("Target theme for demo seeding: [{$slug}]");

        match ($slug) {
            'sarangenge' => $this->call('db:seed', ['--class' => SarangengeThemeDemoSeeder::class]),
            default => $this->fallbackGenericSeed($slug),
        };

        return self::SUCCESS;
    }

    private function fallbackGenericSeed(string $slug): int
    {
        $this->warn("Theme [{$slug}] does not have a dedicated demo seeder class. Falling back to theme:install-sample...");

        return $this->call('theme:install-sample', [
            'slug' => $slug,
            '--force' => (bool) $this->option('force'),
        ]);
    }
}
