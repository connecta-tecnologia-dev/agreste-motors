<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['payment', 'receipt', 'transfer']);
            $table->enum('origin_type', ['customer', 'partner', 'employee', 'bank', 'cash', 'other']);
            $table->integer('origin_id')->nullable();
            $table->enum('destination_type', ['customer', 'partner', 'employee', 'bank', 'cash', 'other']);
            $table->integer('destination_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->timestamp('date')->default(now());
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
        Schema::dropIfExists('transactions');
    }
};
