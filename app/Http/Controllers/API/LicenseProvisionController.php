<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProvisionLicenseRequest;
use App\Http\Resources\LicenseKeyResource;
use App\Models\Brand;
use App\Models\License;
use App\Services\LicenseProvisionService;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\ErrorResource;
use Illuminate\Http\Request;



class LicenseProvisionController extends Controller
{
    protected LicenseProvisionService $service;

    public function __construct(LicenseProvisionService $service)
    {
        $this->service = $service;
    }

    /**
 * Provision a license key for a customer
 *
 * @group Licenses
 * @urlParam brand int required The brand ID. Example: 1
 * @bodyParam customer_email string required Customer email address. Example: customer@test.com
 * @bodyParam licenses array required Array of licenses to provision. Example: [{"product_code":"ACME_PLUGIN","expires_at":"2026-12-26","seat_limit":3}]
 *
 * @response 200 {
 *   "license_key": "f8e730fd-3a83-4286-9bff-5b53d3f7c246",
 *   "customer_email": "user@example.com",
 *   "brand": {
 *       "id": 1,
 *       "name": "Acme Corp",
 *       "slug": "acme"
 *   },
 *   "licenses": [
 *       {
 *           "product": "Rank Math",
 *           "expires_at": "2026-12-31T00:00:00.000000Z",
 *           "seat_limit": 1,
 *           "remaining_seats": 0,
 *           "status": "valid",
 *           "is_valid": true
 *       }
 *   ],
 *   "created_at": "2025-12-26T08:18:24.000000Z"
 * }
 * @response 400 {
 *   "error": "Error message",
 *   "code": 400
 * }
 *
 * POST /brands/{brand}/licenses
 */

   public function store(ProvisionLicenseRequest $request, Brand $brand): JsonResponse
{
    try {
        // Provision the license key
        $licenseKey = $this->service->provision(
            $brand,
            $request->customer_email,
            $request->licenses
        );

        $licenseKey = $licenseKey->fresh(['brand', 'licenses.product']);

        return response()->json(new LicenseKeyResource($licenseKey));
    } catch (Throwable $e) {
        return response()->json(new ErrorResource($e), $e->getCode() ?: 400);
    }
}

/**
 * Update license status
 *
 * @group Licenses
 * @urlParam license int required The license ID. Example: 1
 * @bodyParam status string required New status for the license. Example: valid
 * @bodyParam expires_at string nullable New expiry date in Y-m-d format. Example: 2026-12-31
 *
 * @response 200 {
 *   "status": "valid",
 *   "expires_at": "2026-12-31"
 * }
 *
 * PATCH /licenses/{license}/status
 */
    public function updateStatus(Request $request, License $license): JsonResponse
    {
        try {
            $license = $this->service->updateStatus(
                $license,
                $request->input('status'),
                $request->input('expires_at')
            );

            return response()->json([
                'status' => $license->status,
                'expires_at' => $license->expires_at,
            ]);
        } catch (Throwable $e) {
            return response()->json(new ErrorResource($e), $e->getCode() ?: 400);
        }
    }

   /**
 * Renew a license
 *
 * @group Licenses
 * @urlParam license int required The license ID. Example: 1
 * @bodyParam months int Number of months to extend. Default: 12. Example: 6
 *
 * @response 200 {
 *   "status": "valid",
 *   "expires_at": "2027-06-26"
 * }
 *
 * PATCH /licenses/{license}/renew
 */
    public function renew(Request $request, License $license): JsonResponse
    {
        try {
            $months = (int) $request->input('months', 12);
            $license = $this->service->renew($license, $months);

            return response()->json([
                'status' => $license->status,
                'expires_at' => $license->expires_at,
            ]);
        } catch (Throwable $e) {
            return response()->json(new ErrorResource($e), $e->getCode() ?: 400);
        }
    }
}
