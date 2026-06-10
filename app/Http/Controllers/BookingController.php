<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Room;
use App\Models\BookingRoom;
use App\Models\Schedule;
use App\Models\Kegiatan;

class BookingController extends Controller
{
    // =====================================
    // FORM BOOKING PERKULIAHAN
    // =====================================

    public function createPerkuliahan(Request $request)
    {
        $rooms = Room::all();

        // Ambil schedule untuk dropdown mata kuliah & dosen
        $schedules = Schedule::select('mata_kuliah', 'dosen')
            ->distinct()
            ->orderBy('mata_kuliah')
            ->get();

        // Jika ada room_id dari query string (dari tombol Booking di daftar/detail)
        $selectedRoom = null;
        if ($request->filled('room_id')) {
            $selectedRoom = Room::find($request->room_id);
        }

        return view('bookings.perkuliahan', compact('rooms', 'schedules', 'selectedRoom'));
    }

    // =====================================
    // CEK KETERSEDIAAN (AJAX realtime)
    // =====================================

    public function cekKetersediaan(Request $request)
    {
        $roomId    = $request->room_id;
        $tanggal   = $request->tanggal;
        $jamMulai  = $request->jam_mulai;
        $jamSelesai = $request->jam_selesai;

        if (!$roomId || !$tanggal || !$jamMulai || !$jamSelesai) {
            return response()->json(['status' => 'incomplete']);
        }

        $room = Room::find($roomId);
        if (!$room) {
            return response()->json(['status' => 'not_found']);
        }

        // Konversi hari
        $hariInggris  = date('l', strtotime($tanggal));
        $convertHari  = [
            'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu',
        ];
        $hariIndonesia = $convertHari[$hariInggris] ?? null;

        // Cek bentrok schedule tetap
        $bentrokSchedule = Schedule::where('room_id', $roomId)
            ->where('hari', $hariIndonesia)
            ->where(function ($q) use ($jamMulai, $jamSelesai) {
                $q->where('jam_mulai', '<', $jamSelesai)
                  ->where('jam_selesai', '>', $jamMulai);
            })
            ->first();

        if ($bentrokSchedule) {
            return response()->json([
                'status'   => 'conflict',
                'message'  => 'Bentrok dengan jadwal kuliah',
                'detail'   => $bentrokSchedule->mata_kuliah . ' (' . substr($bentrokSchedule->jam_mulai, 0, 5) . ' - ' . substr($bentrokSchedule->jam_selesai, 0, 5) . ')',
            ]);
        }

        // Cek bentrok booking lain yg approved
        $bentrokBooking = Booking::join('booking_rooms', 'bookings.booking_id', '=', 'booking_rooms.booking_id')
            ->where('booking_rooms.room_id', $roomId)
            ->whereDate('tanggal', $tanggal)
            ->where('status', 'approved')
            ->where(function ($q) use ($jamMulai, $jamSelesai) {
                $q->where('jam_mulai', '<', $jamSelesai)
                  ->where('jam_selesai', '>', $jamMulai);
            })
            ->first();

        if ($bentrokBooking) {
            return response()->json([
                'status'  => 'conflict',
                'message' => 'Ruangan sudah dibooking',
                'detail'  => substr($bentrokBooking->jam_mulai, 0, 5) . ' - ' . substr($bentrokBooking->jam_selesai, 0, 5),
            ]);
        }

        return response()->json(['status' => 'available', 'message' => 'Ruangan tersedia pada slot yang dipilih!']);
    }

    // =====================================
    // SIMPAN BOOKING PERKULIAHAN
    // =====================================

    public function storePerkuliahan(Request $request)
    {
        $request->validate([
            'room_id'     => 'required',
            'tanggal'     => 'required|date',
            'jam_mulai'   => 'required',
            'jam_selesai' => 'required',
        ]);

        $hariInggris   = date('l', strtotime($request->tanggal));
        $convertHari   = [
            'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu',
        ];
        $hariIndonesia = $convertHari[$hariInggris];

        $bentrokSchedule = Schedule::where('room_id', $request->room_id)
            ->where('hari', $hariIndonesia)
            ->where(function ($q) use ($request) {
                $q->where('jam_mulai', '<', $request->jam_selesai)
                  ->where('jam_selesai', '>', $request->jam_mulai);
            })
            ->exists();

        if ($bentrokSchedule) {
            return back()->with('error', 'Ruangan sedang dipakai jadwal kuliah!');
        }

        $bentrokBooking = Booking::join('booking_rooms', 'bookings.booking_id', '=', 'booking_rooms.booking_id')
            ->where('booking_rooms.room_id', $request->room_id)
            ->whereDate('tanggal', $request->tanggal)
            ->where('status', 'approved')
            ->where(function ($q) use ($request) {
                $q->where('jam_mulai', '<', $request->jam_selesai)
                  ->where('jam_selesai', '>', $request->jam_mulai);
            })
            ->exists();

        if ($bentrokBooking) {
            return back()->with('error', 'Ruangan sudah dibooking pada waktu tersebut!');
        }

        $booking = Booking::create([
            'user_id'     => session('user')->user_id,
            'kegiatan_id' => null,
            'tanggal'     => $request->tanggal,
            'jam_mulai'   => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'jenis'       => 'perkuliahan',
            'status'      => 'approved',
            'surat'       => null,
            'approved_by' => null,
            'approved_at' => now(),
        ]);

        BookingRoom::create([
            'booking_id' => $booking->booking_id,
            'room_id'    => $request->room_id,
        ]);

        return redirect('/dashboard')->with('success', 'Booking perkuliahan berhasil!');
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
        $tanggalBooking = strtotime($request->tanggal);
        $minimalTanggal = strtotime('+2 days');

        if ($tanggalBooking < $minimalTanggal) {
            return back()->with('error', 'Booking kegiatan minimal H-2!');
        }

        if (!$request->hasFile('surat')) {
            return back()->with('error', 'File surat wajib diupload!');
        }

        if (!$request->has('room_id')) {
            return back()->with('error', 'Pilih minimal 1 ruangan!');
        }

        foreach ($request->room_id as $roomId) {
            $bentrokBooking = Booking::join('booking_rooms', 'bookings.booking_id', '=', 'booking_rooms.booking_id')
                ->where('booking_rooms.room_id', $roomId)
                ->where('bookings.status', 'approved')
                ->whereDate('tanggal', $request->tanggal)
                ->where(function ($q) use ($request) {
                    $q->where('jam_mulai', '<', $request->jam_selesai)
                      ->where('jam_selesai', '>', $request->jam_mulai);
                })
                ->exists();

            if ($bentrokBooking) {
                $room = Room::find($roomId);
                return back()->with('error', "Ruangan {$room->nama_ruangan} sudah dibooking!");
            }
        }

        $pathSurat = $request->file('surat')->store('surat', 'public');

        $kegiatan = Kegiatan::create([
            'nama_kegiatan' => $request->nama_kegiatan,
            'deskripsi'     => $request->deskripsi,
            'penyelenggara' => $request->penyelenggara,
        ]);

        $booking = Booking::create([
            'user_id'     => session('user')->user_id,
            'kegiatan_id' => $kegiatan->kegiatan_id,
            'tanggal'     => $request->tanggal,
            'jam_mulai'   => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'jenis'       => 'kegiatan',
            'status'      => 'pending',
            'surat'       => $pathSurat,
            'approved_by' => null,
            'approved_at' => null,
        ]);

        foreach ($request->room_id as $roomId) {
            BookingRoom::create([
                'booking_id' => $booking->booking_id,
                'room_id'    => $roomId,
            ]);
        }

        return redirect('/dashboard')->with('success', 'Booking kegiatan berhasil, menunggu persetujuan!');
    }

    // =====================================
    // LIST BOOKING PENDING (admin)
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
        $rooms   = $booking->rooms;

        foreach ($rooms as $room) {
            $bentrok = Booking::join('booking_rooms', 'bookings.booking_id', '=', 'booking_rooms.booking_id')
                ->where('booking_rooms.room_id', $room->room_id)
                ->where('bookings.status', 'approved')
                ->whereDate('bookings.tanggal', $booking->tanggal)
                ->where('bookings.booking_id', '!=', $booking->booking_id)
                ->where(function ($q) use ($booking) {
                    $q->where('bookings.jam_mulai', '<', $booking->jam_selesai)
                      ->where('bookings.jam_selesai', '>', $booking->jam_mulai);
                })
                ->exists();

            if ($bentrok) {
                return back()->with('error', 'Ruangan ' . $room->nama_ruangan . ' sudah dipakai booking lain yang telah disetujui.');
            }
        }

        $booking->status      = 'approved';
        $booking->approved_by = session('user')->user_id;
        $booking->approved_at = now();
        $booking->save();

        return back()->with('success', 'Booking berhasil diapprove.');
    }

    // =====================================
    // REJECT BOOKING
    // =====================================

    public function rejectBooking($id)
    {
        $booking              = Booking::find($id);
        $booking->status      = 'rejected';
        $booking->approved_by = session('user')->user_id;
        $booking->approved_at = now();
        $booking->save();

        return back()->with('success', 'Booking berhasil ditolak.');
    }

    // =====================================
    // RIWAYAT BOOKING USER
    // =====================================

    public function myBookings()
    {
        $userId   = session('user')->user_id;
        $bookings = Booking::where('user_id', $userId)
            ->with('rooms')
            ->orderBy('booking_id', 'desc')
            ->get();

        return view('bookings.history', compact('bookings'));
    }

    // =====================================
    // ALL BOOKINGS (admin)
    // =====================================

    public function allBookings()
    {
        $bookings = Booking::with(['rooms', 'user', 'approver'])
            ->orderBy('booking_id', 'desc')
            ->get();

        return view('admin.bookings.all', compact('bookings'));
    }

    public function downloadTemplate()
    {
        $path = public_path('template/template_surat_peminjaman.docx');
        return response()->download($path);
    }
}