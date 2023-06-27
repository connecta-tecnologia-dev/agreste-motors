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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('type_id')->nullable()->constrained('vehicle_types')->onDelete('set null');
            $table->foreignId('brand_id')->nullable()->constrained('brands')->onDelete('set null');
            $table->foreignId('model_id')->nullable()->constrained('vehicle_models')->nullable()->onDelete('set null');
            $table->foreignId('color_id')->nullable()->constrained('colors')->onDelete('set null');
            $table->integer('model_year');
            $table->integer('manufacture_year');
            $table->integer('mileage');
            $table->string('power');
            $table->integer('doors')->nullable();
            $table->foreignId('transmission_id')->nullable()->constrained('transmissions')->onDelete('set null');
            $table->foreignId('fuel_id')->nullable()->constrained('fuels')->onDelete('set null');
            $table->foreignId('owner_id')->nullable()->constrained('customers')->onDelete('set null');
            $table->string('version');
            $table->string('chassis')->unique();
            $table->string('plate')->unique();
            $table->string('renavam')->unique();
            $table->text('note')->nullable();
            $table->foreignId('creator')->constrained('users');
            $table->foreignId('modifier')->nullable()->constrained('users');
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
        Schema::dropIfExists('vehicles');
    }
};
