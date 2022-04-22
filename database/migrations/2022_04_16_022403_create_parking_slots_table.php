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
        Schema::create('parking_slots', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('parking_lot_id')->unsigned();
            $table->string('name', 50);
            $table->text('distance');
            $table->integer('type');
            $table->timestamps();

            $table->foreign('parking_lot_id')->references('id')->on('parking_lots')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('parking_slots', function (Blueprint $table) {
            $table->dropForeign('parking_slots_parking_lot_id_foreign'); 
            $table->dropIndex('parking_slots_parking_lot_id_foreign');
        });

        Schema::dropIfExists('parking_slots');
    }
};
