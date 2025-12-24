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

            // Prevent duplicate licenses for the same product & customer
            $existing = License::where('license_key_id', $licenseKey->id)
                ->where('product_id', $product->id)
                ->whereIn('status', ['valid', 'expired'])
                ->first();

            if (!$existing) {
                License::create([
                    'license_key_id' => $licenseKey->id,
                    'product_id' => $product->id,
                    'status' => 'valid',
                    'expires_at' => $licenseData['expires_at'],
                    'seat_limit' => $licenseData['seat_limit'] ?? 1,
                ]);
            }
        }

        return $licenseKey;
    }

    public function updateStatus(License $license, string $status, ?string $newExpiryDate = null): License
    {
        $license->status = $status;
        if ($newExpiryDate) {
            $license->expires_at = Carbon::parse($newExpiryDate);
        }
        $license->save();
        return $license;
    }

    public function renew(License $license, int $months = 12): License
    {
        $currentExpiry = $license->expires_at ? Carbon::parse($license->expires_at) : now();
        $license->expires_at = $currentExpiry->addMonths($months);
        $license->status = 'valid';
        $license->save();
        return $license;
    }
}
