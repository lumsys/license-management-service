<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ActivateLicenseRequest;
use App\Services\LicenseActivationService;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\ErrorResource;

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
     */
    public function activate(ActivateLicenseRequest $request): JsonResponse
{
    try {
        $this->service->activate(
            $request->license_key,
            $request->product_code,
            $request->instance_id
        );

        return response()->json(['status' => 'activated']);
    } catch (Throwable $e) {
        return response()->json(new ErrorResource($e), $e->getCode() ?: 400);
    }
}

public function deactivate(ActivateLicenseRequest $request): JsonResponse
{
    try {
        $this->service->deactivate(
            $request->license_key,
            $request->instance_id
        );

        return response()->json(['status' => 'deactivated']);
    } catch (Throwable $e) {
        return response()->json(new ErrorResource($e), $e->getCode() ?: 400);
    }
}

}
