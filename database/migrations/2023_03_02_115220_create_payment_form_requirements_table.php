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
        Schema::create('payment_form_requirements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_form_id')->constrained('payment_forms');
            $table->string('name');
            $table->json('options')->nullable();
            $table->unique(['payment_form_id','name']);
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
        Schema::dropIfExists('payment_form_requirements');
    }
};
