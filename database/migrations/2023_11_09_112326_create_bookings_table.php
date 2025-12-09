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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->integer('member_id');
            $table->integer('barber_id');
            $table->string('booking_time');
            $table->string('booking_date');
            $table->integer('price')->nullable();
            $table->integer('dis_price')->default(0);
            $table->integer('total_price')->default(0);
            $table->text('image');
            $table->text('custom_location')->nullable();
            $table->string('status', 100);
            $table->json('additional_customers')->nullable();
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
        Schema::dropIfExists('bookings');
    }
};
