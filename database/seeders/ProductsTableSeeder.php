<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $product = Product::create([
                'name' => 'Product ' . $i,
                'slug' => 'product-' . $i,
                'sku' => 'SKU' . $i,
                'brand_id' => rand(1, 5),
                'category_id' => rand(1, 5),
                'status' => 'active',
            ]);
            $product->addMedia(public_path('assets/test' . $i . '.jpeg'))->preservingOriginal()
                ->toMediaCollection('product_images');
        }
    }
}
