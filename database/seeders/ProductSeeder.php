<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Brand;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Get the brand created by BrandSeeder
        $brand = Brand::where('slug', 'acme')->first();

        if (!$brand) {
            $this->command->error('Brand not found. Please run BrandSeeder first.');
            return;
        }

        // All products for the brand
        $products = [
            [
                'name' => 'Acme Plugin',
                'code' => 'ACME_PLUGIN',
            ],
            [
                'name' => 'Rank Math',
                'code' => 'RANKMATH',
            ],
            [
                'name' => 'Content AI',
                'code' => 'CONTENT_AI',
            ],
        ];

        foreach ($products as $productData) {
            Product::firstOrCreate(
                [
                    'brand_id' => $brand->id,
                    'code' => $productData['code'],
                ],
                [
                    'name' => $productData['name'],
                ]
            );
        }

        $this->command->info('Products seeded successfully for brand: ' . $brand->name);
    }
}
