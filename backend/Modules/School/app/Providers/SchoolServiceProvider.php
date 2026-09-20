<?php

declare(strict_types=1);

namespace Modules\School\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class SchoolServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $moduleRoot = dirname(__DIR__, 2);
        $this->mergeConfigFrom($moduleRoot.'/config/school.php', 'school');
    }

    public function boot(): void
    {
        $moduleRoot = dirname(__DIR__, 2);

        $this->loadMigrationsFrom($moduleRoot.'/database/migrations');
        $this->loadViewsFrom($moduleRoot.'/resources/views', 'school');

        Route::middleware('api')
            ->prefix('api')
            ->group($moduleRoot.'/routes/api.php');
    }
}
