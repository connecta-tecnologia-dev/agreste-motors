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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('cpf')->unique();
            $table->string('rg');
            $table->string('emitter');
            $table->unique(['rg', 'emitter']);
            $table->string('cep');
            $table->string('address');
            $table->string('number');
            $table->string('district');
            $table->string('city');
            $table->string('state');
            $table->foreignId('employee_function_id')->constrained('employee_functions');
            $table->decimal('salary', 10, 2);
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
        Schema::dropIfExists('employees');
    }
};
