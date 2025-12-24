<?php

namespace Database\Seeders;

use App\Models\{
    Brand,
    Product,
    LicenseKey,
    License,
    ApiKey
};
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brand = Brand::create([
            'name' => 'Acme Corp',
            'slug' => 'acme',
        ]);

        ApiKey::create([
            'brand_id' => $brand->id,
            'key' => 'test-api-key',
        ]);

        $product = Product::create([
            'brand_id' => $brand->id,
            'name' => 'Acme Plugin',
            'code' => 'ACME_PLUGIN',
        ]);

        $licenseKey = LicenseKey::create([
            'brand_id' => $brand->id,
            'key' => 'TEST-LICENSE-KEY',
            'customer_email' => 'customer@test.com',
        ]);

        License::create([
            'license_key_id' => $licenseKey->id,
            'product_id' => $product->id,
            'status' => 'valid',
            'expires_at' => now()->addYear(),
            'seat_limit' => 2,
        ]);
    }
}
