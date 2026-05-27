<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $table = 'schedules';

    protected $primaryKey = 'schedule_id';

    protected $fillable = [
        'room_id',
        'mata_kuliah',
        'dosen',
        'hari',
        'jam_mulai',
        'jam_selesai',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_id');
    }
}