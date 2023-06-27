<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('debts', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['payment', 'receipt']);
            $table->enum('payment_type', ['financing', 'credit_card', 'promissory', 'checks', 'slips']);
            $table->integer('installments');
            $table->json('data');
            $table->decimal('total', 10, 2);
            $table->decimal('paid', 10, 2)->default(0);
            $table->boolean('status')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('debts');
    }
};
