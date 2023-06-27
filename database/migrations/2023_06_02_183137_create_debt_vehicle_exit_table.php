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
        Schema::create('debt_vehicle_exit', function (Blueprint $table) {
            $table->foreignId('debt_id')->constrained('debts')->onDelete('cascade');
            $table->foreignId('vehicle_exit_id')->constrained('vehicle_exits')->onDelete('cascade');
            $table->decimal('value', 10, 2);
            $table->primary(['debt_id', 'vehicle_exit_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('debt_vehicle_exit');
    }
};
