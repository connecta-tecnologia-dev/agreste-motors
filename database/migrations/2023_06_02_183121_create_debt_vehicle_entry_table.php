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
        Schema::create('debt_vehicle_entry', function (Blueprint $table) {
            $table->foreignId('debt_id')->constrained('debts')->onDelete('cascade');
            $table->foreignId('vehicle_entry_id')->constrained('vehicle_entries')->onDelete('cascade');
            $table->decimal('value', 10, 2);
            $table->primary(['debt_id', 'vehicle_entry_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('debt_vehicle_entry');
    }
};
