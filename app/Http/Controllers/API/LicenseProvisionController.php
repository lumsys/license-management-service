<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProvisionLicenseRequest;
use App\Http\Resources\LicenseKeyResource;
use App\Models\Brand;
use App\Services\LicenseProvisionService;
use Illuminate\Http\JsonResponse;

class LicenseProvisionController extends Controller
{
    protected LicenseProvisionService $service;

    public function __construct(LicenseProvisionService $service)
    {
        $this->service = $service;
    }

    public function store(
        ProvisionLicenseRequest $request,
        Brand $brand
    ): JsonResponse {
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
    }
}
