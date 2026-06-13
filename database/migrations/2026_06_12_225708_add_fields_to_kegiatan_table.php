<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kegiatan', function (Blueprint $table) {
            $table->date('tanggal_selesai')->nullable()->after('penyelenggara');
            $table->integer('perkiraan_peserta')->nullable()->after('tanggal_selesai');
        });
    }

    public function down(): void
    {
        Schema::table('kegiatan', function (Blueprint $table) {
            $table->dropColumn([
                'tanggal_selesai',
                'perkiraan_peserta'
            ]);
        });
    }
};