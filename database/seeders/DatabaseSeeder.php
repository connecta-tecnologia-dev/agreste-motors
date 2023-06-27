<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(FunctionsSeed::class);
        $this->call(UsersSeed::class);
        $this->call(ColorsSeed::class);
        $this->call(BrandsSeed::class);
        $this->call(PaymentsSeed::class);
        $this->call(PaymentRequerementsSeed::class);
        $this->call(CashiersSeed::class);
        $this->call(CustomersSeed::class);
    }
}
