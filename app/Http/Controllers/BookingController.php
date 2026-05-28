<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Room;
use App\Models\BookingRoom;
use App\Models\Schedule;

class BookingController extends Controller
{
    // =========================
    // FORM BOOKING PERKULIAHAN
    // =========================

    public function createPerkuliahan()
    {
        $rooms = Room::all();

        return view('bookings.perkuliahan', compact('rooms'));
    }

    // =========================
    // SIMPAN BOOKING PERKULIAHAN
    // =========================

    public function storePerkuliahan(Request $request)
    {
        // =========================
        // KONVERSI HARI INDONESIA
        // =========================

        $hariInggris = date('l', strtotime($request->tanggal));

        $convertHari = [
            'Monday'    => 'Senin',
            'Tuesday'   => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday'  => 'Kamis',
            'Friday'    => 'Jumat',
            'Saturday'  => 'Sabtu',
            'Sunday'    => 'Minggu',
        ];

        $hariIndonesia = $convertHari[$hariInggris];

        // =========================
        // VALIDASI BENTROK SCHEDULE
        // =========================

        $bentrokSchedule = Schedule::where('room_id', $request->room_id)

            ->where('hari', $hariIndonesia)

            ->where(function ($query) use ($request) {

                $query->where('jam_mulai', '<', $request->jam_selesai)

                      ->where('jam_selesai', '>', $request->jam_mulai);

            })

            ->exists();

        // =========================
        // JIKA BENTROK SCHEDULE
        // =========================

        if ($bentrokSchedule) {

            return "Ruangan sedang dipakai jadwal kuliah!";
        }

        // =========================
        // VALIDASI BENTROK BOOKING
        // =========================

        $bentrokBooking = Booking::join(
                'booking_rooms',
                'bookings.booking_id',
                '=',
                'booking_rooms.booking_id'
            )

            ->where('booking_rooms.room_id', $request->room_id)

            ->whereDate('tanggal', $request->tanggal)

            ->where(function ($query) use ($request) {

                $query->where('jam_mulai', '<', $request->jam_selesai)

                      ->where('jam_selesai', '>', $request->jam_mulai);

            })

            ->exists();

        // =========================
        // JIKA BENTROK BOOKING
        // =========================

        if ($bentrokBooking) {

            return "Ruangan sudah dibooking!";
        }

        // =========================
        // SIMPAN BOOKING
        // =========================

        $booking = Booking::create([

            'user_id' => session('user')->user_id,

            'kegiatan_id' => null,

            'tanggal' => $request->tanggal,

            'jam_mulai' => $request->jam_mulai,

            'jam_selesai' => $request->jam_selesai,

            'jenis' => 'perkuliahan',

            'status' => 'approved',

            'surat' => null,

            'approved_by' => null,

            'approved_at' => now(),

        ]);

        // =========================
        // SIMPAN BOOKING ROOM
        // =========================

        BookingRoom::create([

            'booking_id' => $booking->booking_id,

            'room_id' => $request->room_id,

        ]);

        return "Booking berhasil!";
    }
}