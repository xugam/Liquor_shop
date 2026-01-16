<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Whiskey', 'slug' => 'whiskey'],
            ['name' => 'Vodka', 'slug' => 'vodka'],
            ['name' => 'Beer', 'slug' => 'beer'],
            ['name' => 'Rum', 'slug' => 'rum'],
            ['name' => 'Tequila', 'slug' => 'tequila'],
        ];

        foreach ($categories as $category) {
            $category = Category::create($category);
            $category->addMedia(public_path('assets/test' . $category->id . '.jpeg'))->preservingOriginal()
                ->toMediaCollection('category_images');
        }
    }
}
