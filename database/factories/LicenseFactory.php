<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\License;
use App\Models\LicenseKey;
use App\Models\Product;

class LicenseFactory extends Factory
{
    protected $model = License::class;

    public function definition()
    {
        return [
            'license_key_id' => LicenseKey::factory(), 
            'product_id' => Product::factory(),       
            'status' => 'valid',
            'expires_at' => now()->addYear(),
            'seat_limit' => 2,
        ];
    }
}
