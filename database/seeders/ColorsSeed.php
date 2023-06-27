<?php

namespace Database\Seeders;

use App\Models\Color;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ColorsSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Color::create([
            'name' => 'Azul',
        ]);

        Color::create([
            'name' => 'Vermelho',
        ]);

        Color::create([
            'name' => 'Verde',
        ]);

        Color::create([
            'name' => 'Amarelo',
        ]);

        Color::create([
            'name' => 'Laranja',
        ]);
    }
}
