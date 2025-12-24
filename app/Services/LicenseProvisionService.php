<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\License;
use App\Models\LicenseKey;
use App\Models\Product;
use Illuminate\Support\Str;

class LicenseProvisionService
{
    public function provision(
        Brand $brand,
        string $customerEmail,
        array $licenses
    ): LicenseKey {
        $licenseKey = LicenseKey::firstOrCreate(
            [
                'brand_id' => $brand->id,
                'customer_email' => $customerEmail,
            ],
            [
                'key' => (string) Str::uuid(),
            ]
        );

        foreach ($licenses as $licenseData) {
            $product = Product::where('brand_id', $brand->id)
                ->where('code', $licenseData['product_code'])
                ->firstOrFail();

            License::create([
                'license_key_id' => $licenseKey->id,
                'product_id' => $product->id,
                'status' => 'valid',
                'expires_at' => $licenseData['expires_at'],
                'seat_limit' => $licenseData['seat_limit'] ?? 1,
            ]);
        }

        return $licenseKey;
    }
}
