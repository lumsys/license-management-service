<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\BrandLicenseResource;
use App\Models\LicenseKey;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BrandLicenseQueryController extends Controller
{
    public function index(
        Request $request
    ): AnonymousResourceCollection {
        $licenses = LicenseKey::with('licenses.product', 'brand')
            ->where('customer_email', $request->query('email'))
            ->paginate(10);

        return BrandLicenseResource::collection($licenses);
    }
}
