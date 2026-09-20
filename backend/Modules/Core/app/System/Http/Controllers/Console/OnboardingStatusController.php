<?php

declare(strict_types=1);

namespace Modules\Core\System\Http\Controllers\Console;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\System\Http\Controllers\BaseApiController;
use Modules\Core\System\Models\Setting;
use Modules\Core\System\Models\User;
use Modules\School\Models\Institution\School;

class OnboardingStatusController extends BaseApiController
{
    public function show(Request $request): JsonResponse
    {
        /** @var User|null $user */
        $user = $request->user();

        $siteNameRaw = Setting::get('site_name', '');
        $siteName = is_string($siteNameRaw) && trim($siteNameRaw) !== ''
            ? trim($siteNameRaw)
            : '';
        if ($siteName === '') {
            $appName = Setting::get('app_name', '');
            if (is_string($appName) && trim($appName) !== '') {
                $siteName = trim($appName);
            } else {
                $defaultName = config('app.name');
                $siteName = is_string($defaultName) ? $defaultName : '';
            }
        }

        $schoolConfigured = false;
        try {
            if (class_exists(School::class)) {
                $schoolConfigured = School::query()->exists();
            }
        } catch (\Throwable) {
        }

        $steps = [
            'identity' => $siteName !== '',
            'school_profile' => $schoolConfigured,
            'security' => (bool) Setting::get('enable_2fa', false),
        ];

        $completed = count(array_filter($steps));
        $dismissed = $user ? (bool) $user->getPreference('onboarding.dismissed', false) : false;

        return $this->success([
            'dismissed' => $dismissed,
            'steps' => $steps,
            'school_configured' => $schoolConfigured,
            'site_name' => $siteName,
            'complete' => $completed === count($steps),
            'progress_percent' => (int) round(($completed / max(1, count($steps))) * 100),
        ], 'Onboarding status');
    }

    public function dismiss(Request $request): JsonResponse
    {
        /** @var User|null $user */
        $user = $request->user();
        if (! $user) {
            return $this->unauthorized();
        }

        $user->setPreference('onboarding.dismissed', true);
        $user->save();

        return $this->success(['dismissed' => true], 'Onboarding checklist dismissed');
    }
}
