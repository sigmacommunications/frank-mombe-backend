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
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('event_type')->nullable();
            $table->date('event_date')->nullable();
            $table->integer('number_of_people')->nullable();
            $table->string('staff_preference')->nullable();
            $table->string('service_location')->nullable();
            $table->integer('number_of_staff_member')->nullable();
            $table->text('parking_instructions')->nullable();
            $table->text('add_ons')->nullable();
            $table->string('preferred_stylist_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            //
        });
    }
};
