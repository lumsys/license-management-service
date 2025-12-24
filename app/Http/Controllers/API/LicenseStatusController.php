<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\LicenseStatusResource;
use App\Models\LicenseKey;
use Illuminate\Http\JsonResponse;

class LicenseStatusController extends Controller
{
    public function show(string $key): JsonResponse
    {
        $licenseKey = LicenseKey::with('licenses.product')
            ->where('key', $key)
            ->firstOrFail();

        return response()->json(
            new LicenseStatusResource($licenseKey)
        );
    }
}
