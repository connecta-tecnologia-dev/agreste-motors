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
        Schema::create('vehicle_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles');
            $table->foreignId('vehicle_entry_id')->constrained('vehicle_entries')->onDelete('cascade');
            $table->foreignId('vehicle_exit_id')->nullable()->constrained('vehicle_exits')->cascadeOnUpdate()->nullOnDelete();
            $table->string('location')->nullable();
            $table->boolean('finished')->default(false);
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
        Schema::dropIfExists('vehicle_histories');
    }
};
