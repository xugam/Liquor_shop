<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [
            'Ktm',
            'Bkt',
            'Lalitpur',
            'Pkr',
            'Bhaktapur'
        ];

        foreach ($locations as $name) {
            Location::create([
                'name' => $name,
                'slug' => str($name)->slug(),
                'type' => 'warehouse',
                'address' => '123 Main St',
                'phone_no' => '1234567890',
            ]);
        }
    }
}
