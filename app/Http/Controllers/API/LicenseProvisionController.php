<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProvisionLicenseRequest;
use App\Http\Resources\LicenseKeyResource;
use App\Models\Brand;
use App\Services\LicenseProvisionService;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\ErrorResource;

class LicenseProvisionController extends Controller
{
    protected LicenseProvisionService $service;

    public function __construct(LicenseProvisionService $service)
    {
        $this->service = $service;
    }

    public function store(ProvisionLicenseRequest $request, Brand $brand): JsonResponse
{
    try {
        $licenseKey = $this->service->provision(
            $brand,
            $request->customer_email,
            $request->licenses
        );

        return response()->json(
            new LicenseKeyResource(
                $licenseKey->load('brand', 'licenses.product')
            )
        );
    } catch (Throwable $e) {
        return response()->json(new ErrorResource($e), $e->getCode() ?: 400);
    }
}


 // --- Update status ---
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

    // --- Renew license ---
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
