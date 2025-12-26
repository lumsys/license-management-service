<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Brand;

class BrandFactory extends Factory
{
    protected $model = Brand::class;

   public function definition()
{
    $name = $this->faker->company;

    return [
        'name' => $name,
        'slug' => \Str::slug($name), 
    ];
}
}
