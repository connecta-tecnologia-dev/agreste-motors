<?php

namespace Database\Seeders;

use App\Models\Cashier;
use App\Models\Color;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CashiersSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Cashier::create([
            'name' => 'Cofre',
            'balance' => 0,
        ]);

        Cashier::create([
            'name' => 'Interno',
            'balance' => 150000,
        ]);

        Cashier::create([
            'name' => 'Despesas',
            'balance' => 0,
        ]);
    }
}
