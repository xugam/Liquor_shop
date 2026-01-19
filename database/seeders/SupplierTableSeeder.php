<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SupplierTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            ['name' => 'Supplier 1', 'email' => 'supplier1@example.com', 'phone' => '1234567890', 'address' => '123 Main St', 'slug' => 'supplier-1', 'status' => 'active'],
            ['name' => 'Supplier 2', 'email' => 'supplier2@example.com', 'phone' => '0987654321', 'address' => '456 Elm St', 'slug' => 'supplier-2', 'status' => 'active'],
            ['name' => 'Supplier 3', 'email' => 'supplier3@example.com', 'phone' => '1122334455', 'address' => '789 Oak St', 'slug' => 'supplier-3', 'status' => 'active'],
            ['name' => 'Supplier 4', 'email' => 'supplier4@example.com', 'phone' => '5566778899', 'address' => '012 Pine St', 'slug' => 'supplier-4', 'status' => 'active'],
            ['name' => 'Supplier 5', 'email' => 'supplier5@example.com', 'phone' => '9988776655', 'address' => '321 Birch St', 'slug' => 'supplier-5', 'status' => 'active'],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}
