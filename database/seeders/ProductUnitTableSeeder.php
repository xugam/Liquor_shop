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
        $productUnits = [
            ['name' => 'Bottle', 'slug' => 'bottle', 'is_base_unit' => true, 'conversion_factor' => 1],
            ['name' => 'Carton', 'slug' => 'carton', 'is_base_unit' => false, 'conversion_factor' => 12],
            ['name' => 'Case', 'slug' => 'case', 'is_base_unit' => false, 'conversion_factor' => 24],
            ['name' => 'Box', 'slug' => 'box', 'is_base_unit' => false, 'conversion_factor' => 12],
        ];
        ProductUnit::insert($productUnits);
    }
}
