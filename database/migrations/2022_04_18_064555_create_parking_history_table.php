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
        Schema::create('parking_history', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('parking_lot_id')->unsigned();
            $table->bigInteger('parking_slot_id')->unsigned();
            $table->bigInteger('continuous_rate_id')->nullable();
            $table->string('license_plate', 10);
            $table->integer('vehicle_size');
            $table->integer('slot_type');
            $table->string('status', 20);
            $table->float('rate')->default(0.00);
            $table->float('total_hours')->nullable();
            $table->float('paid_hours')->nullable();
            $table->float('balance_hours')->nullable()->default(0);
            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime')->nullable();
            $table->timestamps();

            $table->index('parking_lot_id');
            $table->index('parking_slot_id');
            $table->foreign('parking_lot_id')->references('id')->on('parking_lots')->onDelete('cascade');
            $table->foreign('parking_slot_id')->references('id')->on('parking_slots')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('parking_history', function (Blueprint $table) {
            $table->dropForeign('parking_history_parking_lot_id_foreign'); 
            $table->dropForeign('parking_history_parking_slot_id_foreign');
            $table->dropIndex('parking_history_parking_lot_id_index');
            $table->dropIndex('parking_history_parking_slot_id_index');
        });

        Schema::dropIfExists('parking_history');
    }
};
