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
        Schema::create('booking_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');            
            $table->string('name')->nullable();
            $table->integer('service_id')->nullable();
            $table->string('any_allergies')->nullable();
            $table->string('add_ons')->nullable();
            $table->string('reference_image')->nullable();
            $table->foreign('booking_id')->references('id')->on('bookings')->onDelete('cascade');
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
        Schema::dropIfExists('booking_members');
    }
};
