<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\LicenseStatusResource;
use App\Models\LicenseKey;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\ErrorResource;

class LicenseStatusController extends Controller
{
    public function show(string $key): JsonResponse
{
    try {
        $licenseKey = LicenseKey::with('licenses.product')
            ->where('key', $key)
            ->firstOrFail();

        $status = (new \App\Services\LicenseQueryService())->getStatus($licenseKey);

        return response()->json($status);
    } catch (Throwable $e) {
        return response()->json(new ErrorResource($e), $e->getCode() ?: 400);
    }
}
}
