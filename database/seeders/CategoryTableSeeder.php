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
            'Whiskey',
            'Vodka',
            'Beer',
            'Rum',
            'Tequila',
        ];

        foreach ($categories as $name) {
            Category::create(['name' => $name]);
        }
    }
}
