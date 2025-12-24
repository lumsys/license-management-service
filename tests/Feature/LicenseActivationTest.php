<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LicenseActivationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_license_can_be_activated(): void
    {
        $response = $this->postJson('/api/v1/licenses/activate', [
            'license_key' => 'TEST-LICENSE-KEY',
            'product_code' => 'ACME_PLUGIN',
            'instance_id' => 'site-1',
        ]);

        $response->assertStatus(200);
    }

    public function test_activation_fails_when_seats_exceeded(): void
    {
        $this->postJson('/api/v1/licenses/activate', [
            'license_key' => 'TEST-LICENSE-KEY',
            'product_code' => 'ACME_PLUGIN',
            'instance_id' => 'site-1',
        ]);

        $this->postJson('/api/v1/licenses/activate', [
            'license_key' => 'TEST-LICENSE-KEY',
            'product_code' => 'ACME_PLUGIN',
            'instance_id' => 'site-2',
        ]);

        $response = $this->postJson('/api/v1/licenses/activate', [
            'license_key' => 'TEST-LICENSE-KEY',
            'product_code' => 'ACME_PLUGIN',
            'instance_id' => 'site-3',
        ]);

        $response->assertStatus(403);
    }
}
