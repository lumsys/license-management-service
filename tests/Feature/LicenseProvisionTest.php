<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LicenseProvisionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_brand_can_provision_license(): void
    {
        $response = $this->withHeader('API-KEY', 'test-api-key')
            ->postJson('/api/v1/brands/1/licenses', [
                'customer_email' => 'new@test.com',
                'licenses' => [
                    [
                        'product_code' => 'ACME_PLUGIN',
                        'expires_at' => now()->addMonth()->toDateString(),
                        'seat_limit' => 3,
                    ],
                ],
            ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'license_key',
                    'customer_email',
                    'licenses',
                ],
            ]);
    }
}
