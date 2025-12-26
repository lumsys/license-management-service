<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ApiKey;
use Illuminate\Support\Str;

class ApiKeySeeder extends Seeder
{
    public function run(): void
    {
        // INTERNAL (Group.one)
        ApiKey::firstOrCreate(
            [
                'role' => 'internal',
                'brand_id' => null,
            ],
            [
                'key' => 'int_' . Str::random(40),
                'is_active' => true,
            ]
        );

        // BRAND (Acme Corp)
        ApiKey::firstOrCreate(
            [
                'role' => 'brand',
                'brand_id' => 1,
            ],
            [
                'key' => 'br_' . Str::random(40),
                'is_active' => true,
            ]
        );
    }
}
