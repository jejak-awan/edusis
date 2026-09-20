<?php

declare(strict_types=1);

namespace Modules\School\Http\Controllers\Api\Common;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Context;
use Modules\School\Models\Institution\School;

class BaseController extends Controller
{
    use AuthorizesRequests;

    /**
     * Dapatkan school_id aktif dari Laravel Context atau request context institusi.
     */
    protected function getSchoolId(): int
    {
        $contextId = Context::get('school_id');
        if ($contextId && is_numeric($contextId)) {
            return (int) $contextId;
        }

        $headerId = request()->header('X-School-ID') ?? request()->header('X-School-Id');
        if ($headerId && is_numeric($headerId)) {
            return (int) $headerId;
        }

        if (auth()->check() && ! empty(auth()->user()->school_id)) {
            return (int) auth()->user()->school_id;
        }

        $defaultSchool = School::where('is_active', true)->first() ?? School::first();

        return $defaultSchool ? (int) $defaultSchool->id : 1;
    }

    public function sendResponse(mixed $result, string $message = 'Success', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $result,
            'message' => $message,
        ], $code);
    }

    public function sendError(string $error, array $errorMessages = [], int $code = 400): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (! empty($errorMessages)) {
            $response['errors'] = $errorMessages;
        }

        return response()->json($response, $code);
    }
}
