<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Product;
use App\Models\Brand;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition()
    {
        return [
            'brand_id' => Brand::factory(), 
            'name' => $this->faker->word,
            'code' => strtoupper($this->faker->unique()->lexify('?????')),
        ];
    }
}
