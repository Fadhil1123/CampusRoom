<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    protected $table = 'kegiatan';
    protected $primaryKey = 'kegiatan_id';

    protected $fillable = [
        'nama_kegiatan',
        'deskripsi',
        'penyelenggara',
        'tanggal_selesai',
        'perkiraan_peserta',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'kegiatan_id', 'kegiatan_id');
    }
}