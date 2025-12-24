<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\License;
use App\Models\LicenseKey;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

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

    Log::channel('licenses')->info('License key provisioned', [
        'brand_id' => $brand->id,
        'customer_email' => $customerEmail,
        'license_key_id' => $licenseKey->id,
        'was_created' => $licenseKey->wasRecentlyCreated,
    ]);

    foreach ($licenses as $licenseData) {
        $product = Product::where('brand_id', $brand->id)
            ->where('code', $licenseData['product_code'])
            ->firstOrFail();

        $existing = License::where('license_key_id', $licenseKey->id)
            ->where('product_id', $product->id)
            ->whereIn('status', ['valid', 'expired'])
            ->first();

        if ($existing) {
           
            Log::channel('licenses')->info('License skipped (already exists)', [
                'license_key_id' => $licenseKey->id,
                'product_code' => $product->code,
                'license_id' => $existing->id,
            ]);
            continue;
        }

        $license = License::create([
            'license_key_id' => $licenseKey->id,
            'product_id' => $product->id,
            'status' => 'valid',
            'expires_at' => $licenseData['expires_at'],
            'seat_limit' => $licenseData['seat_limit'] ?? 1,
        ]);

        Log::channel('licenses')->info('License created', [
            'license_key_id' => $licenseKey->id,
            'license_id' => $license->id,
            'product_code' => $product->code,
            'expires_at' => $license->expires_at,
            'seat_limit' => $license->seat_limit,
        ]);
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
