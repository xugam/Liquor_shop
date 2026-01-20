<?php

namespace Database\Seeders;

use App\Models\ProductUnit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductUnitTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 10; $i++) {
            $productUnits = [
                ['product_id' => $i + 1, 'name' => 'Bottle', 'slug' => 'bottle', 'is_base_unit' => true, 'conversion_factor' => 1, 'cost_price' => rand(100, 2000), 'selling_price' =>  rand(2000, 3000)],
                ['product_id' => $i + 1, 'name' => 'Carton', 'slug' => 'carton', 'is_base_unit' => false, 'conversion_factor' => 12, 'cost_price' => rand(100, 2000), 'selling_price' => rand(2000, 3000)],
                ['product_id' => $i + 1, 'name' => 'Case', 'slug' => 'case', 'is_base_unit' => false, 'conversion_factor' => 24, 'cost_price' => rand(100, 2000), 'selling_price' => rand(2000, 3000)],
                ['product_id' => $i + 1, 'name' => 'Box', 'slug' => 'box', 'is_base_unit' => false, 'conversion_factor' => 12, 'cost_price' => rand(100, 2000), 'selling_price' =>  rand(2000, 3000)],
            ];
            ProductUnit::insert($productUnits);
        }
    }
}
