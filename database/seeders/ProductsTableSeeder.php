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
            Product::create([
                'name' => 'Product ' . $i,
                'slug' => 'product-' . $i,
                'cost_price' => $i * 10,
                'selling_price' => $i * 20,
                'image' => public_path('assets/beer.jpeg'),
                'sku' => 'SKU' . $i,
                'brand_id' => rand(1, 5),
                'category_id' => rand(1, 5),
                'supplier_id' => rand(1, 5),
                'base_unit_id' => rand(1, 5),
            ]);
        }
    }
}
