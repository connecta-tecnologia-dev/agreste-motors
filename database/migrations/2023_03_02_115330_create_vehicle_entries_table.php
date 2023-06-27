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
        Schema::create('vehicle_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles');
            $table->integer('mileage')->default(0);
            $table->foreignId('supplier_id')->nullable()->default(null)->constrained('customers');
            $table->foreignId('owner_id')->constrained('customers');
            $table->decimal('amount_paid', 10, 2)->default(0);
            $table->decimal('minimum_value', 10, 2)->default(0);
            $table->decimal('value', 10, 2)->default(0);
            $table->timestamp('date');
            $table->text('note')->nullable();
            $table->boolean('finished')->default(false);
            $table->foreignId('creator')->constrained('users');
            $table->foreignId('modifier')->nullable()->constrained('users');
            $table->foreignId('finisher')->nullable()->constrained('users');
            $table->timestamps();
        });

        DB::statement('
            CREATE TRIGGER before_insert_entry
            BEFORE INSERT ON vehicle_entries
            FOR EACH ROW
            BEGIN
                DECLARE vehicle_count INT;

                SELECT COUNT(*)
                INTO vehicle_count
                FROM vehicle_histories
                WHERE vehicle_id = NEW.vehicle_id
                AND vehicle_exit_id IS NULL;

                IF vehicle_count > 0 THEN
                    SIGNAL SQLSTATE \'45000\'
                    SET MESSAGE_TEXT = \'O veículo já está no estoque!\';
                END IF;
            END;
        ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS before_insert_entry');
        Schema::dropIfExists('vehicle_entries');
    }
};
