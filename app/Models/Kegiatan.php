<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    use HasFactory;

    protected $table = 'kegiatan';

    protected $primaryKey = 'kegiatan_id';

    protected $fillable = [
        'nama_kegiatan',
        'deskripsi',
        'penyelenggara',
    ];

    public function bookings()
    {
        return $this->hasMany(
            Booking::class,
            'kegiatan_id'
        );
    }
}