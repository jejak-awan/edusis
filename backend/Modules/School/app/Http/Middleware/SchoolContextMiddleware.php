<?php

declare(strict_types=1);

namespace Modules\School\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Modules\School\Exceptions\SchoolModuleException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware untuk menginjeksi konteks institusi/sekolah (tenant) ke dalam Laravel 11 Context.
 * Memastikan seluruh query dan mutasi data terisolasi secara otomatis per tenant.
 *
 * Prioritas resolusi school_id:
 *  1. Header X-School-ID / X-School-Id (untuk API inter-service)
 *  2. Atribut school_id pada user yang terautentikasi
 *
 * Dilarang keras menggunakan fallback School::first() — lihat ADR-031 Bab 3.
 */
class SchoolContextMiddleware
{
    /**
     * Tangani request masuk dan tetapkan school_id pada context request.
     *
     * @throws SchoolModuleException Jika school_id tidak dapat di-resolve dan diperlukan.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $schoolId = $request->header('X-School-ID') ?? $request->header('X-School-Id');

        if (! $schoolId && $request->user()) {
            $schoolId = $request->user()->school_id ?? null;
        }

        if ($schoolId && is_numeric($schoolId)) {
            Context::add('school_id', (int) $schoolId);
        }

        // school_id tidak wajib di setiap endpoint (mis. endpoint publik PPDB / Kiosk Voting).
        // Controller yang membutuhkan school_id wajib melakukan validasi eksplisit via Policy atau Service.

        return $next($request);
    }
}
