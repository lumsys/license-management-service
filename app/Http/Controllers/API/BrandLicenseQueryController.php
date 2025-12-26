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
 * List licenses by customer email
 *
 * @group Licenses
 * @queryParam email string required Customer email to filter. Example: user@example.com
 *
 * @response 200 {
 *   "data": [
 *       {
 *           "license_key": "f8e730fd-3a83-4286-9bff-5b53d3f7c246",
 *           "customer_email": "user@example.com",
 *           "brand": {
 *               "id": 1,
 *               "name": "Acme Corp",
 *               "slug": "acme"
 *           },
 *           "licenses": [
 *               {
 *                   "product": "Rank Math",
 *                   "expires_at": "2026-12-31T00:00:00.000000Z",
 *                   "seat_limit": 1,
 *                   "remaining_seats": 0,
 *                   "status": "valid",
 *                   "is_valid": true
 *               }
 *           ]
 *       }
 *   ]
 * }
 *
 * GET /brands/licenses
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
