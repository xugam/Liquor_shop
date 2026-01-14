<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrandTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = ['Jack Daniels', 'Absolut', 'Heineken', 'Chateau Margaux', 'Bacardi'];
        foreach ($brands as $name) {
            Brand::create(['name' => $name]);
        }
    }
}
