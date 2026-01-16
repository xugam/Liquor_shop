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
        $brands = [
            ['name' => 'Jack Daniels', 'slug' => 'jack-daniels'],
            ['name' => 'Absolut', 'slug' => 'absolut'],
            ['name' => 'Heineken', 'slug' => 'heineken'],
            ['name' => 'Chateau Margaux', 'slug' => 'chateau-margaux'],
            ['name' => 'Bacardi', 'slug' => 'bacardi'],
        ];
        foreach ($brands as $brand) {
            $brand = Brand::create($brand);
            $brand->addMedia(public_path('assets/test' . $brand->id . '.jpeg'))->preservingOriginal()
                ->toMediaCollection('brand_images');
        }
    }
}
