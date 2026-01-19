<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\LocationProduct;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductLocationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();
        $locations = Location::all();

        foreach ($products as $product) {
            foreach ($locations as $location) {
                LocationProduct::create([
                    'product_id' => $product->id,
                    'location_id' => $location->id,
                    'quantity' => rand(10, 100),
                    'reorder_level' => 10,
                ]);
            }
        }
    }
}
