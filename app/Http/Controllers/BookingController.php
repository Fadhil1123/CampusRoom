<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Room;
use App\Models\BookingRoom;
use App\Models\Schedule;

class BookingController extends Controller
{
    // =====================================
    // FORM BOOKING PERKULIAHAN
    // =====================================

    public function createPerkuliahan()
    {
        $rooms = Room::all();

        return view('bookings.perkuliahan', compact('rooms'));
    }

    // =====================================
    // SIMPAN BOOKING PERKULIAHAN
    // =====================================

    public function storePerkuliahan(Request $request)
    {
        // =========================
        // KONVERSI HARI INDONESIA
        // =========================

        $hariInggris = date('l', strtotime($request->tanggal));

        $convertHari = [
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
            'Sunday' => 'Minggu',
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

    // =====================================
    // FORM BOOKING KEGIATAN
    // =====================================

    public function createKegiatan()
    {
        $rooms = Room::all();

        return view('bookings.kegiatan', compact('rooms'));
    }

    // =====================================
    // SIMPAN BOOKING KEGIATAN
    // =====================================

    public function storeKegiatan(Request $request)
    {
        // =========================
        // VALIDASI H-2
        // =========================

        $tanggalBooking = strtotime($request->tanggal);

        $minimalTanggal = strtotime('+2 days');

        if ($tanggalBooking < $minimalTanggal) {

            return "Booking kegiatan minimal H-2!";
        }

        // =========================
        // VALIDASI FILE
        // =========================

        if (!$request->hasFile('surat')) {

            return "File surat wajib diupload!";
        }

        // =========================
        // VALIDASI ROOM DIPILIH
        // =========================

        if (!$request->has('room_id')) {

            return "Pilih minimal 1 ruangan!";
        }

        // =========================
        // VALIDASI BENTROK SEMUA ROOM
        // =========================

        foreach ($request->room_id as $roomId) {

            $bentrokBooking = Booking::join(
                'booking_rooms',
                'bookings.booking_id',
                '=',
                'booking_rooms.booking_id'
            )

                ->where('booking_rooms.room_id', $roomId)

                ->whereDate('tanggal', $request->tanggal)

                ->where(function ($query) use ($request) {

                    $query->where('jam_mulai', '<', $request->jam_selesai)

                        ->where('jam_selesai', '>', $request->jam_mulai);

                })

                ->exists();

            // =========================
            // JIKA ADA ROOM BENTROK
            // =========================

            if ($bentrokBooking) {

                $room = Room::find($roomId);

                return "Ruangan {$room->nama_ruangan} sudah dibooking!";
            }
        }

        // =========================
        // UPLOAD SURAT
        // =========================

        $pathSurat = $request->file('surat')->store('surat', 'public');

        // =========================
        // SIMPAN BOOKING
        // =========================

        $booking = Booking::create([

            'user_id' => session('user')->user_id,

            'kegiatan_id' => null,

            'tanggal' => $request->tanggal,

            'jam_mulai' => $request->jam_mulai,

            'jam_selesai' => $request->jam_selesai,

            'jenis' => 'kegiatan',

            'status' => 'pending',

            'surat' => $pathSurat,

            'approved_by' => null,

            'approved_at' => null,

        ]);

        // =========================
        // SIMPAN MULTI ROOM
        // =========================

        foreach ($request->room_id as $roomId) {

            BookingRoom::create([

                'booking_id' => $booking->booking_id,

                'room_id' => $roomId,

            ]);
        }

        return "Booking kegiatan berhasil!";
    }

    // =====================================
    // LIST BOOKING PENDING
    // =====================================

    public function pendingBookings()
    {
        $bookings = Booking::where('status', 'pending')->get();

        return view('admin.bookings.pending', compact('bookings'));
    }

    // =====================================
    // APPROVE BOOKING
    // =====================================

    public function approveBooking($id)
    {
        $booking = Booking::find($id);

        $booking->status = 'approved';

        $booking->approved_by = session('user')->user_id;

        $booking->approved_at = now();

        $booking->save();

        return "Booking berhasil diapprove!";
    }

    // =====================================
    // REJECT BOOKING
    // =====================================

    public function rejectBooking($id)
    {
        $booking = Booking::find($id);

        $booking->status = 'rejected';

        $booking->save();

        return "Booking berhasil ditolak!";
    }

    // =====================================
    // RIWAYAT BOOKING USER
    // =====================================

    public function myBookings()
    {
        $userId = session('user')->user_id;

        $bookings = Booking::where('user_id', $userId)

            ->with('rooms')

            ->orderBy('booking_id', 'desc')

            ->get();

        return view('bookings.history', compact('bookings'));
    }

    // =====================================
    // ALL BOOKINGS ADMIN
    // =====================================

    public function allBookings()
    {
    $bookings = Booking::with([
            'rooms',
            'user'
        ])

        ->orderBy('booking_id', 'desc')

        ->get();

    return view(
        'admin.bookings.all',
        compact('bookings')
    );
}
}