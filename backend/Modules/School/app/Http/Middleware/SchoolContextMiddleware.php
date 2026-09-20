<?php

declare(strict_types=1);

namespace Modules\School\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Modules\School\Models\Institution\School;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware untuk menginjeksi konteks institusi/sekolah (tenant) ke dalam Laravel 11 Context.
 * Memastikan seluruh query dan mutasi data terisolasi secara otomatis per tenant.
 */
class SchoolContextMiddleware
{
    /**
     * Tangani request masuk dan tetapkan school_id pada context request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $schoolId = $request->header('X-School-ID') ?? $request->header('X-School-Id');

        if (! $schoolId && $request->user()) {
            $schoolId = $request->user()->school_id ?? null;
        }

        if (! $schoolId) {
            $defaultSchool = School::where('is_active', true)->first() ?? School::first();
            $schoolId = $defaultSchool?->id;
        }

        if ($schoolId && is_numeric($schoolId)) {
            Context::add('school_id', (int) $schoolId);
        }

        return $next($request);
    }
}
