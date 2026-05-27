<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingRoom extends Model
{
    use HasFactory;

    protected $table = 'booking_rooms';

    protected $primaryKey = 'booking_room_id';

    /**
     * Table only has `created_at`; disable Eloquent automatic timestamps
     * to avoid writing `updated_at` which doesn't exist.
     */
    public $timestamps = false;

    protected $fillable = [
        'booking_id',
        'room_id',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }
}