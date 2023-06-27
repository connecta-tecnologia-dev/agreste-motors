<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrandsSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Brand::create([
            'name' => 'Fiat',
        ]);

        Brand::create([
            'name' => 'Volkswagen',
        ]);

        Brand::create([
            'name' => 'Honda',
        ]);

        Brand::create([
            'name' => 'Toyota',
        ]);

        Brand::create([
            'name' => 'Hyundai',
        ]);
    }
}
