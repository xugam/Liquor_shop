<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\LocationProduct;
use App\Models\Product;
use App\Models\ProductUnit;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductLocationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productUnits = ProductUnit::all();
        $locations = Location::all();

        foreach ($productUnits as $productUnit) {
            foreach ($locations as $location) {
                LocationProduct::updateOrCreate([
                    'unit_id' => $productUnit->id,
                    'location_id' => $location->id,
                ], [
                    'quantity' => rand(10, 100),
                    'reorder_level' => 10,
                ]);
            }
        }
    }
}
