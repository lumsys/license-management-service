<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\BrandLicenseResource;
use App\Http\Resources\ErrorResource;
use App\Services\LicenseQueryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class BrandLicenseQueryController extends Controller
{
    protected LicenseQueryService $service;

    public function __construct(LicenseQueryService $service)
    {
        $this->service = $service;
    }

    /**
     * US6: List licenses by customer email
     * - Brand scope: restricted to brand
     * - Internal scope: cross-brand
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $email = $request->query('email');

            if (!$email) {
                abort(422, 'Customer email is required');
            }

            // Authorization scope decision
            $brandId = $request->get('role') === 'internal'
                ? null
                : $request->get('brand_id');

            // Delegated to query service
            $licenses = $this->service->listByEmail(
                $email,
                $brandId
            );

            return response()->json(
                BrandLicenseResource::collection($licenses)
            );
        } catch (Throwable $e) {
            return response()->json(
                new ErrorResource($e),
                $e->getCode() ?: 400
            );
        }
    }
}
