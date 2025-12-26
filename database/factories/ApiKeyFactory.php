<?php

namespace Database\Factories;

use App\Models\ApiKey;
use App\Models\Brand;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ApiKeyFactory extends Factory
{
    protected $model = ApiKey::class;

    public function definition(): array
    {
        return [
            'brand_id' => Brand::factory(),
            'key' => Str::uuid(),
            'role' => 'brand',
            'is_active' => true,
        ];
    }
}
