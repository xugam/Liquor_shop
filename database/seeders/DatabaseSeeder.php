<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {

        $this->call([
            LocationTableSeeder::class,
            CategoryTableSeeder::class,
            BrandTableSeeder::class,
            ProductUnitTableSeeder::class,
            ProductsTableSeeder::class,
            ProductLocationTableSeeder::class,
            SupplierTableSeeder::class,
            ProductLocationTableSeeder::class,
        ]);
    }
}
