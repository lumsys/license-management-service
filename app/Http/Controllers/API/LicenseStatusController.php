<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\LicenseStatusResource;
use App\Models\LicenseKey;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\ErrorResource;

class LicenseStatusController extends Controller
{

    /**
 * Get the status of a license
 *
 * @group Licenses
 * @urlParam key string required License key. Example: TEST-LICENSE-KEY
 *
 * @response 200 {
 *   "license_key": "f8e730fd-3a83-4286-9bff-5b53d3f7c246",
 *   "is_valid": true,
 *   "entitlements": [
 *       {
 *           "product": "Rank Math",
 *           "expires_at": "2026-12-31T00:00:00.000000Z",
 *           "seat_limit": 1,
 *           "remaining_seats": 1,
 *           "status": "valid",
 *           "is_valid": true
 *       }
 *   ]
 * }
 *
 * GET /licenses/{key}
 */

    public function show(string $key): JsonResponse
{
    try {
        $licenseKey = LicenseKey::with('licenses.product')
            ->where('key', $key)
            ->firstOrFail();

        return response()->json(new LicenseStatusResource($licenseKey));
    } catch (Throwable $e) {
        return response()->json(new ErrorResource($e), $e->getCode() ?: 400);
    }
}
}
