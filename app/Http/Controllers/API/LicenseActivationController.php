<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActivateLicenseRequest;
use App\Services\LicenseActivationService;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\ActivationResource;

class LicenseActivationController extends Controller
{
    protected LicenseActivationService $service;

    public function __construct(LicenseActivationService $service)
    {
        $this->service = $service;
    }

   /**
 * Activate a license
 *
 * @group Licenses
 * @bodyParam license_key string required The license key to activate. Example: TEST-LICENSE-KEY
 * @bodyParam product_code string required The product code to activate. Example: ACME_PLUGIN
 * @bodyParam instance_id string required Unique instance ID for activation. Example: site-1
 *
 * @response 200 {
 *   "instance_id": "site-1",
 *   "activated_at": "2025-12-26T08:00:00.000000Z"
 * }
 * @response 403 {
 *   "message": "No available seats"
 * }
 * @response 403 {
 *   "message": "License not valid"
 * }
 *
 * POST /licenses/activate
 */
   public function activate(ActivateLicenseRequest $request): JsonResponse
{
    try {
        $activation = $this->service->activate(
            $request->license_key,
            $request->product_code,
            $request->instance_id
        );

        return response()->json(new ActivationResource($activation));
    } catch (\Throwable $e) {
    return response()->json(new ErrorResource($e), 400);
}

}


/**
 * Deactivate a license
 *
 * @group Licenses
 * @bodyParam license_key string required The license key to deactivate. Example: TEST-LICENSE-KEY
 * @bodyParam instance_id string required Instance ID to deactivate. Example: site-1
 *
 * @response 200 {
 *   "instance_id": "site-1",
 *   "activated_at": "2025-12-26T08:00:00.000000Z"
 * }
 * @response 404 {
 *   "error": "Activation not found",
 *   "code": 404
 * }
 *
 * POST /licenses/deactivate
 */

public function deactivate(ActivateLicenseRequest $request): JsonResponse
{
    try {
        $activation = $this->service->deactivate(
            $request->license_key,
            $request->instance_id
        );

        return response()->json(new ActivationResource($activation));
    } catch (Throwable $e) {
        return response()->json(new ErrorResource($e), $e->getCode() ?: 400);
    }
}


}
