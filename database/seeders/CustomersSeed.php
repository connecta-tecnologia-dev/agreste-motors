<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomersSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Customer::create([
            'name' => 'Agreste',
            'cpf' => '00.000.000/0000-00',
            'address' => '',
            'number' => '',
            'district' => '',
            'city' => '',
            'state' => '',
            'cep' => '',
            'creator' => 1,
        ]);
    }
}
