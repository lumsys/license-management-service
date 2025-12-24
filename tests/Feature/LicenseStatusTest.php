<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LicenseStatusTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_license_status_endpoint(): void
    {
        $response = $this->getJson('/api/v1/licenses/TEST-LICENSE-KEY');

        $response
            ->assertStatus(200)
            ->assertJsonFragment([
                'license_key' => 'TEST-LICENSE-KEY',
                'is_valid' => true,
            ]);
    }
}
