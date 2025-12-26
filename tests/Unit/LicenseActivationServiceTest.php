<?php

namespace Tests\Unit;

use App\Services\LicenseActivationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LicenseActivationServiceTest extends TestCase
{
    use RefreshDatabase;

    protected LicenseActivationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        $this->service = app(LicenseActivationService::class);
    }

    public function test_seat_limit_enforced(): void
    {
        $this->service->activate(
            'TEST-LICENSE-KEY',
            'ACME_PLUGIN',
            'instance-1'
        );

        $this->service->activate(
            'TEST-LICENSE-KEY',
            'ACME_PLUGIN',
            'instance-2'
        );

       $this->expectException(\App\Exceptions\NoAvailableSeatsException::class);

        $this->service->activate(
            'TEST-LICENSE-KEY',
            'ACME_PLUGIN',
            'instance-3'
        );
    }
}
