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
        Schema::create('bookings', function (Blueprint $table) {
            $table->increments('booking_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('kegiatan_id')->nullable();
            $table->date('tanggal');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->enum('jenis', ['perkuliahan', 'kegiatan']);
            $table->enum('status', ['pending', 'approved', 'rejected'])->nullable()->default('pending');
            $table->string('surat', 255)->nullable();
            $table->unsignedInteger('approved_by')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->foreign('user_id', 'fk_bookings_user')
                ->references('user_id')
                ->on('users')
                ->cascadeOnDelete();

            $table->foreign('kegiatan_id', 'fk_bookings_kegiatan')
                ->references('kegiatan_id')
                ->on('kegiatan')
                ->nullOnDelete();

            $table->foreign('approved_by', 'fk_bookings_approved_by')
                ->references('user_id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};