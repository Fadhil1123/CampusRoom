<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('booking_rooms', function (Blueprint $table) {
            $table->increments('booking_room_id');
            $table->unsignedInteger('booking_id');
            $table->unsignedInteger('room_id');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('booking_id', 'fk_booking_rooms_booking')
                ->references('booking_id')
                ->on('bookings')
                ->cascadeOnDelete();

            $table->foreign('room_id', 'fk_booking_rooms_room')
                ->references('room_id')
                ->on('rooms')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_rooms');
    }
};