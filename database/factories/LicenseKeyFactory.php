<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\LicenseKey;
use App\Models\Brand;

class LicenseKeyFactory extends Factory
{
    protected $model = LicenseKey::class;

    public function definition()
    {
        return [
            'brand_id' => Brand::factory(), 
            'key' => $this->faker->uuid,
            'customer_email' => $this->faker->safeEmail,
        ];
    }
}
