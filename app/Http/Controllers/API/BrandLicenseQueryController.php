<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\BrandLicenseResource;
use App\Models\LicenseKey;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Http\Resources\ErrorResource;

class BrandLicenseQueryController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection|JsonResponse
{
    try {
        $licenses = LicenseKey::with('licenses.product', 'brand')
            ->where('brand_id', $request->user()->brand_id)
            ->where('customer_email', $request->query('email'))
            ->paginate(10);

        return BrandLicenseResource::collection($licenses);
    } catch (Throwable $e) {
        return response()->json(new ErrorResource($e), $e->getCode() ?: 400);
    }
}
}
