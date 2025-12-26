<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Brand;
use App\Models\Product;
use App\Models\LicenseKey;
use App\Models\License;

class LicenseStatusTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create brand and product
        $this->brand = Brand::factory()->create(['id' => 1]);
        $this->product = Product::factory()->create([
            'brand_id' => $this->brand->id,
            'code' => 'ACME_PLUGIN',
            'name' => 'Acme Plugin',
        ]);

        // Create a license key
        $this->licenseKey = LicenseKey::factory()->create([
            'brand_id' => $this->brand->id,
            'key' => 'TEST-LICENSE-KEY',
            'customer_email' => 'user@test.com',
        ]);

        // Create a license linked to product and key
        $this->license = License::factory()->create([
            'license_key_id' => $this->licenseKey->id,
            'product_id' => $this->product->id,
            'status' => 'valid',
            'expires_at' => now()->addYear(),
            'seat_limit' => 2,
        ]);
    }

   public function test_license_status_endpoint(): void
{
    $response = $this->getJson('/api/v1/licenses/TEST-LICENSE-KEY');

    $response
        ->assertStatus(200)
        ->assertJsonStructure([
            'license_key',
            'is_valid',
            'entitlements' => [
                '*' => [
                    'product',
                    'expires_at',
                    'seat_limit',
                    'remaining_seats',
                    'status',
                    'is_valid',
                ],
            ],
        ]);
}


}
