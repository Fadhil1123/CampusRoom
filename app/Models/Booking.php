<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'bookings';

    protected $primaryKey = 'booking_id';

    protected $fillable = [
        'user_id',
        'kegiatan_id',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'jenis',
        'status',
        'surat',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class, 'kegiatan_id');
    }

    public function rooms()
    {
        return $this->belongsToMany(
            Room::class,
            'booking_rooms',
            'booking_id',
            'room_id'
        );
    }

        public function approver()
    {
        return $this->belongsTo(
            User::class,
            'approved_by',
            'user_id'
        );
    }

    protected $casts = [
        'approved_at' => 'datetime',
    ];
}