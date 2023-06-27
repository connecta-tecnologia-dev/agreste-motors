<?php

namespace Database\Seeders;

use App\Models\PaymentFormRequirement;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentRequerementsSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        PaymentFormRequirement::create([
            'payment_form_id' => '2',
            'name' => 'Banco',
            'options' => [ 'type' => 'bank', 'slug' => 'bank'],
        ]);

        PaymentFormRequirement::create([
            'payment_form_id' => '3',
            'name' => 'Financeira',
            'options' => [ 'type' => 'text', 'slug' => 'financial'],
        ]);

        PaymentFormRequirement::create([
            'payment_form_id' => '3',
            'name' => 'Parcelas',
            'options' => [ 'type' => 'text', 'mask' => '##', 'default' => 1, 'slug' => 'installments'],
        ]);

        PaymentFormRequirement::create([
            'payment_form_id' => '3',
            'name' => 'Primeiro Pagamento',
            'options' => [ 'type' => 'date', 'slug' => 'first_payment'],
        ]);

        PaymentFormRequirement::create([
            'payment_form_id' => '3',
            'name' => 'Valor das Parcelas',
            'options' => [ 'type' => 'money', 'slug' => 'installment_value'],
        ]);

        PaymentFormRequirement::create([
            'payment_form_id' => '3',
            'name' => 'Banco',
            'options' => [ 'type' => 'bank', 'slug' => 'bank'],
        ]);

        PaymentFormRequirement::create([
            'payment_form_id' => '4',
            'name' => 'Parcelas',
            'options' => [ 'type' => 'text', 'mask' => '##', 'default' => 1, 'slug' => 'installments'],
        ]);

        PaymentFormRequirement::create([
            'payment_form_id' => '4',
            'name' => 'Primeiro Pagamento',
            'options' => ['type' => 'date', 'slug' => 'first_payment'],
        ]);

        PaymentFormRequirement::create([
            'payment_form_id' => '4',
            'name' => 'Valor das Parcelas',
            'options' => [ 'type' => 'money', 'slug' => 'installment_value'],
        ]);

        PaymentFormRequirement::create([
            'payment_form_id' => '4',
            'name' => 'Banco',
            'options' => [ 'type' => 'bank', 'slug' => 'bank'],
        ]);

        PaymentFormRequirement::create([
            'payment_form_id' => '5',
            'name' => 'Parcelas',
            'options' => [ 'type' => 'text', 'mask' => '##', 'default' => 1, 'slug' => 'installments' ],
        ]);

        PaymentFormRequirement::create([
            'payment_form_id' => '5',
            'name' => 'Primeiro Pagamento',
            'options' => [ 'type' => 'date', 'slug' => 'first_payment' ],
        ]);

        PaymentFormRequirement::create([
            'payment_form_id' => '5',
            'name' => 'Banco',
            'options' => [ 'type' => 'bank', 'slug' => 'bank' ],
        ]);

        PaymentFormRequirement::create([
            'payment_form_id' => '6',
            'name' => 'Parcelas',
            'options' => [ 'type' => 'text', 'mask' => '##', 'default' => 1, 'slug' => 'installments' ],
        ]);

        PaymentFormRequirement::create([
            'payment_form_id' => '6',
            'name' => 'Primeiro Pagamento',
            'options' => [ 'type' => 'date', 'slug' => 'first_payment' ],
        ]);

        PaymentFormRequirement::create([
            'payment_form_id' => '6',
            'name' => 'Banco',
            'options' => [ 'type' => 'bank', 'slug' => 'bank' ],
        ]);

        PaymentFormRequirement::create([
            'payment_form_id' => '7',
            'name' => 'Parcelas',
            'options' => [ 'type' => 'text', 'mask' => '##', 'default' => 1, 'slug' => 'installments' ],
        ]);

        PaymentFormRequirement::create([
            'payment_form_id' => '7',
            'name' => 'Primeiro Pagamento',
            'options' => [ 'type' => 'date', 'slug' => 'first_payment' ],
        ]);

        PaymentFormRequirement::create([
            'payment_form_id' => '7',
            'name' => 'Banco',
            'options' => [ 'type' => 'bank', 'slug' => 'bank' ],
        ]);
        
    }
}
