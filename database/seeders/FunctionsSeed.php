<?php

namespace Database\Seeders;

use App\Models\EmployeeFunction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FunctionsSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        EmployeeFunction::create([
            'name' => 'Despachante',
        ]);
    }
}
