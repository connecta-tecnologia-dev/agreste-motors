<?php

namespace Database\Seeders;

use App\Models\PaymentForm;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentsSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PaymentForm::create([
            'name' => 'Em Espécie',
            'slug' => 'money',
            'type' => ['payment', 'receipt']
        ]);

        PaymentForm::create([
            'name' => 'Transferência Bancaria',
            'slug' => 'bank_transfer',
            'type' => ['payment', 'receipt']
        ]);

        PaymentForm::create([
            'name' => 'Financiamento',
            'slug' => 'financing',
            'type' => ['receipt']
        ]);

        PaymentForm::create([
            'name' => 'Cartão de Crédito',
            'slug' => 'credit_card',
            'type' => ['receipt']
        ]);

        PaymentForm::create([
            'name' => 'Promissórias',
            'slug' => 'promissory',
            'type' => ['receipt']
        ]);

        PaymentForm::create([
            'name' => 'Cheques',
            'slug' => 'checks',
            'type' => ['receipt']
        ]);

        PaymentForm::create([
            'name' => 'Boletos',
            'slug' => 'slips',
            'type' => ['receipt']
        ]);
    }
}
