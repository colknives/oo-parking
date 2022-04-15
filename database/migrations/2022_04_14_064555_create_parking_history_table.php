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
            $table->bigInteger('parking_lot_id');
            $table->bigInteger('parking_slot_id');
            $table->string('license_plate');
            $table->string('status');
            $table->float('rate')->default(0.00);
            $table->boolean('continuous_rate')->default(0);
            $table->dateTime('start_datetime');
            $table->dateTime('end_datetime')->nullable();
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
        Schema::dropIfExists('parking_history');
    }
};
