<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Brand;
use App\Models\Product;
use App\Models\ApiKey;

class LicenseProvisionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
{
    parent::setUp();

    $this->brand = Brand::factory()->create();

    $this->product = Product::factory()->create([
        'brand_id' => $this->brand->id,
        'code' => 'ACME_PLUGIN',
        'name' => 'Acme Plugin',
    ]);

    $this->apiKey = ApiKey::factory()->create([
        'brand_id' => $this->brand->id,
        'role' => 'brand',
        'is_active' => true,
    ]);
}


    public function test_brand_can_provision_license(): void
{
    $response = $this->withHeader('API-KEY', $this->apiKey->key)
        ->postJson("/api/v1/brands/{$this->brand->id}/licenses", [
            'customer_email' => 'new@test.com',
            'licenses' => [
                [
                    'product_code' => $this->product->code,
                    'expires_at' => now()->addMonth()->toDateString(),
                    'seat_limit' => 3,
                ],
            ],
        ]);

    $response
        ->assertStatus(200)
        ->assertJsonStructure([
            'license_key',
            'customer_email',
            'brand' => [
                'id',
                'name',
                'slug',
            ],
            'licenses' => [
                '*' => [
                    'product',
                    'expires_at',
                    'seat_limit',
                    'remaining_seats',
                    'status',
                    'is_valid',
                ],
            ],
            'created_at',
        ]);
}

}
