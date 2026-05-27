<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $table = 'rooms';

    protected $primaryKey = 'room_id';

    protected $fillable = [
        'nama_ruangan',
        'kapasitas',
        'status',
    ];

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'room_id');
    }

    public function bookings()
    {
        return $this->belongsToMany(
            Booking::class,
            'booking_rooms',
            'room_id',
            'booking_id'
        );
    }
}