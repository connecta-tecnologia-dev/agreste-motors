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
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->timestamp('date')->default(now());
            $table->decimal('amount', 10, 2);
            $table->foreignId('vehicle_entry_id')->nullable()->constrained('vehicle_entries')->onDelete('cascade');
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
        Schema::dropIfExists('expenses');
    }
};
