<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Room;
use App\Models\BookingRoom;
use App\Models\Schedule;
use App\Models\Kegiatan;
use Carbon\Carbon;

class BookingController extends Controller
{
    // =====================================
    // FORM BOOKING PERKULIAHAN (STEP 1)
    // =====================================

    public function createPerkuliahan(Request $request)
    {
        $rooms = Room::all();

        $schedules = Schedule::select('mata_kuliah', 'dosen')
            ->distinct()
            ->orderBy('mata_kuliah')
            ->get();

        $selectedRoom = null;
        if ($request->filled('room_id')) {
            $selectedRoom = Room::find($request->room_id);
        }

        return view('bookings.perkuliahan', compact('rooms', 'schedules', 'selectedRoom'));
    }

    // =====================================
    // CEK KETERSEDIAAN (AJAX realtime) - dipakai perkuliahan & kegiatan
    // =====================================

    public function cekKetersediaan(Request $request)
    {
        $roomId     = $request->room_id;
        $tanggal    = $request->tanggal;
        $jamMulai   = $request->jam_mulai;
        $jamSelesai = $request->jam_selesai;

        if (!$roomId || !$tanggal || !$jamMulai || !$jamSelesai) {
            return response()->json(['status' => 'incomplete']);
        }

        $room = Room::find($roomId);
        if (!$room) {
            return response()->json(['status' => 'not_found']);
        }

        $hariInggris  = date('l', strtotime($tanggal));
        $convertHari  = [
            'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu',
        ];
        $hariIndonesia = $convertHari[$hariInggris] ?? null;

        $bentrokSchedule = Schedule::where('room_id', $roomId)
            ->where('hari', $hariIndonesia)
            ->where(function ($q) use ($jamMulai, $jamSelesai) {
                $q->where('jam_mulai', '<', $jamSelesai)
                  ->where('jam_selesai', '>', $jamMulai);
            })
            ->first();

        if ($bentrokSchedule) {
            return response()->json([
                'status'  => 'conflict',
                'message' => 'Bentrok dengan jadwal kuliah',
                'detail'  => $bentrokSchedule->mata_kuliah . ' (' . substr($bentrokSchedule->jam_mulai, 0, 5) . ' - ' . substr($bentrokSchedule->jam_selesai, 0, 5) . ')',
            ]);
        }

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
    // STEP 1 -> STEP 2 PERKULIAHAN: SIMPAN KE SESSION, TAMPIL KONFIRMASI
    // =====================================

    public function konfirmasiPerkuliahan(Request $request)
    {
        $request->validate([
            'room_id'     => 'required',
            'mata_kuliah' => 'required',
            'dosen'       => 'required',
            'tanggal'     => 'required|date',
            'jam_mulai'   => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'keterangan'  => 'nullable|string',
        ]);

        $hariInggris  = date('l', strtotime($request->tanggal));
        $convertHari  = [
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
            return back()->with('error', 'Ruangan sedang dipakai jadwal kuliah!')->withInput();
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
            return back()->with('error', 'Ruangan sudah dibooking pada waktu tersebut!')->withInput();
        }

        session(['booking_draft' => [
            'room_id'     => $request->room_id,
            'mata_kuliah' => $request->mata_kuliah,
            'dosen'       => $request->dosen,
            'tanggal'     => $request->tanggal,
            'jam_mulai'   => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'keterangan'  => $request->keterangan ?: 'Perkuliahan rutin. Mahasiswa diimbau hadir 15 menit sebelum waktu.',
        ]]);

        return redirect('/booking/perkuliahan/konfirmasi');
    }

    public function showKonfirmasiPerkuliahan()
    {
        $draft = session('booking_draft');

        if (!$draft) {
            return redirect('/booking/perkuliahan')->with('error', 'Sesi booking tidak ditemukan, silakan isi ulang form.');
        }

        $room = Room::find($draft['room_id']);

        return view('bookings.konfirmasi-perkuliahan', compact('draft', 'room'));
    }

    public function storePerkuliahan(Request $request)
    {
        $draft = session('booking_draft');

        if (!$draft) {
            return redirect('/booking/perkuliahan')->with('error', 'Sesi booking tidak ditemukan, silakan isi ulang form.');
        }

        $hariInggris  = date('l', strtotime($draft['tanggal']));
        $convertHari  = [
            'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu',
        ];
        $hariIndonesia = $convertHari[$hariInggris];

        $bentrokSchedule = Schedule::where('room_id', $draft['room_id'])
            ->where('hari', $hariIndonesia)
            ->where(function ($q) use ($draft) {
                $q->where('jam_mulai', '<', $draft['jam_selesai'])
                  ->where('jam_selesai', '>', $draft['jam_mulai']);
            })
            ->exists();

        $bentrokBooking = Booking::join('booking_rooms', 'bookings.booking_id', '=', 'booking_rooms.booking_id')
            ->where('booking_rooms.room_id', $draft['room_id'])
            ->whereDate('tanggal', $draft['tanggal'])
            ->where('status', 'approved')
            ->where(function ($q) use ($draft) {
                $q->where('jam_mulai', '<', $draft['jam_selesai'])
                  ->where('jam_selesai', '>', $draft['jam_mulai']);
            })
            ->exists();

        if ($bentrokSchedule || $bentrokBooking) {
            session()->forget('booking_draft');
            return redirect('/booking/perkuliahan')->with('error', 'Maaf, ruangan baru saja dibooking pihak lain. Silakan pilih waktu/ruangan lain.');
        }

        $booking = Booking::create([
            'user_id'     => session('user')->user_id,
            'kegiatan_id' => null,
            'tanggal'     => $draft['tanggal'],
            'jam_mulai'   => $draft['jam_mulai'],
            'jam_selesai' => $draft['jam_selesai'],
            'jenis'       => 'perkuliahan',
            'status'      => 'approved',
            'surat'       => null,
            'approved_by' => null,
            'approved_at' => now(),
        ]);

        BookingRoom::create([
            'booking_id' => $booking->booking_id,
            'room_id'    => $draft['room_id'],
        ]);

        session(['booking_done' => [
            'booking_id'  => $booking->booking_id,
            'room_id'     => $draft['room_id'],
            'mata_kuliah' => $draft['mata_kuliah'],
            'tanggal'     => $draft['tanggal'],
            'jam_mulai'   => $draft['jam_mulai'],
            'jam_selesai' => $draft['jam_selesai'],
        ]]);
        session()->forget('booking_draft');

        return redirect('/booking/perkuliahan/selesai');
    }

    public function selesaiPerkuliahan()
    {
        $done = session('booking_done');

        if (!$done) {
            return redirect('/dashboard');
        }

        $room = Room::find($done['room_id']);

        return view('bookings.selesai-perkuliahan', compact('done', 'room'));
    }

    // =====================================
    // FORM BOOKING KEGIATAN (STEP 1)
    // =====================================

    public function createKegiatan(Request $request)
    {
        $rooms = Room::all();

        $selectedRoom = null;
        if ($request->filled('room_id')) {
            $selectedRoom = Room::find($request->room_id);
        }

        // Tanggal paling awal yang bisa dipilih (H-2)
        $minTanggal = now()->addDays(2)->toDateString();

        return view('bookings.kegiatan', compact('rooms', 'selectedRoom', 'minTanggal'));
    }

    // =====================================
    // STEP 1 -> STEP 2 KEGIATAN: VALIDASI, UPLOAD SEMENTARA, SIMPAN SESSION
    // =====================================

    public function konfirmasiKegiatan(Request $request)
    {
        $request->validate([
            'room_id'           => 'required',
            'nama_kegiatan'     => 'required|string|max:150',
            'penyelenggara'     => 'required|string|max:150',
            'tanggal'           => 'required|date',
            'tanggal_selesai'   => 'required|date|after_or_equal:tanggal',
            'jam_mulai'         => 'required',
            'jam_selesai'       => 'required|after:jam_mulai',
            'deskripsi'         => 'nullable|string',
            'perkiraan_peserta' => 'required|integer|min:1',
            'surat'             => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        // ===== Validasi H-2 =====
        $minimalTanggal = now()->startOfDay()->addDays(2);
        $tanggalKegiatan = Carbon::parse($request->tanggal)->startOfDay();

        if ($tanggalKegiatan->lt($minimalTanggal)) {
            return back()
                ->with('error', 'Tanggal terlalu dekat! Pengajuan booking kegiatan minimal H-2. Tanggal paling awal yang bisa dipilih: ' . $minimalTanggal->locale('id')->translatedFormat('l, d F Y'))
                ->withInput();
        }

        // ===== Validasi bentrok ruangan (approved only) =====
        $bentrokBooking = Booking::join('booking_rooms', 'bookings.booking_id', '=', 'booking_rooms.booking_id')
            ->where('booking_rooms.room_id', $request->room_id)
            ->where('bookings.status', 'approved')
            ->whereDate('bookings.tanggal', $request->tanggal)
            ->where(function ($q) use ($request) {
                $q->where('bookings.jam_mulai', '<', $request->jam_selesai)
                  ->where('bookings.jam_selesai', '>', $request->jam_mulai);
            })
            ->exists();

        if ($bentrokBooking) {
            return back()->with('error', 'Ruangan sudah dibooking pada waktu tersebut!')->withInput();
        }

        // ===== Upload surat sementara ke folder temp =====
        $pathSurat = $request->file('surat')->store('surat/temp', 'public');

        session(['kegiatan_draft' => [
            'room_id'           => $request->room_id,
            'nama_kegiatan'     => $request->nama_kegiatan,
            'penyelenggara'     => $request->penyelenggara,
            'tanggal'           => $request->tanggal,
            'tanggal_selesai'   => $request->tanggal_selesai,
            'jam_mulai'         => $request->jam_mulai,
            'jam_selesai'       => $request->jam_selesai,
            'deskripsi'         => $request->deskripsi,
            'perkiraan_peserta' => $request->perkiraan_peserta,
            'surat_temp'        => $pathSurat,
            'surat_nama_asli'   => $request->file('surat')->getClientOriginalName(),
        ]]);

        return redirect('/booking/kegiatan/konfirmasi');
    }

    // =====================================
    // STEP 2: TAMPILKAN HALAMAN KONFIRMASI KEGIATAN
    // =====================================

    public function showKonfirmasiKegiatan()
    {
        $draft = session('kegiatan_draft');

        if (!$draft) {
            return redirect('/booking/kegiatan')->with('error', 'Sesi booking tidak ditemukan, silakan isi ulang form.');
        }

        $room = Room::find($draft['room_id']);

        return view('bookings.konfirmasi-kegiatan', compact('draft', 'room'));
    }

    // =====================================
    // STEP 2 -> STEP 3: SIMPAN KEGIATAN + BOOKING KE DB (pindahkan file dari temp)
    // =====================================

    public function storeKegiatan(Request $request)
    {
        $draft = session('kegiatan_draft');

        if (!$draft) {
            return redirect('/booking/kegiatan')->with('error', 'Sesi booking tidak ditemukan, silakan isi ulang form.');
        }

        // Re-cek bentrok terakhir (race condition)
        $bentrokBooking = Booking::join('booking_rooms', 'bookings.booking_id', '=', 'booking_rooms.booking_id')
            ->where('booking_rooms.room_id', $draft['room_id'])
            ->where('bookings.status', 'approved')
            ->whereDate('bookings.tanggal', $draft['tanggal'])
            ->where(function ($q) use ($draft) {
                $q->where('bookings.jam_mulai', '<', $draft['jam_selesai'])
                  ->where('bookings.jam_selesai', '>', $draft['jam_mulai']);
            })
            ->exists();

        if ($bentrokBooking) {
            session()->forget('kegiatan_draft');
            return redirect('/booking/kegiatan')->with('error', 'Maaf, ruangan baru saja dibooking pihak lain. Silakan pilih waktu/ruangan lain.');
        }

        // Pindahkan file surat dari temp ke folder permanen
        $pathTemp  = $draft['surat_temp'];
        $pathFinal = str_replace('surat/temp/', 'surat/', $pathTemp);

        if (\Storage::disk('public')->exists($pathTemp)) {
            \Storage::disk('public')->move($pathTemp, $pathFinal);
        }

        $kegiatan = Kegiatan::create([
            'nama_kegiatan'     => $draft['nama_kegiatan'],
            'deskripsi'         => $draft['deskripsi'],
            'penyelenggara'     => $draft['penyelenggara'],
            'tanggal_selesai'   => $draft['tanggal_selesai'],
            'perkiraan_peserta' => $draft['perkiraan_peserta'],
        ]);

        $booking = Booking::create([
            'user_id'     => session('user')->user_id,
            'kegiatan_id' => $kegiatan->kegiatan_id,
            'tanggal'     => $draft['tanggal'],
            'jam_mulai'   => $draft['jam_mulai'],
            'jam_selesai' => $draft['jam_selesai'],
            'jenis'       => 'kegiatan',
            'status'      => 'pending',
            'surat'       => $pathFinal,
            'approved_by' => null,
            'approved_at' => null,
        ]);

        BookingRoom::create([
            'booking_id' => $booking->booking_id,
            'room_id'    => $draft['room_id'],
        ]);

        session(['kegiatan_done' => [
            'booking_id'    => $booking->booking_id,
            'nama_kegiatan' => $draft['nama_kegiatan'],
            'room_id'       => $draft['room_id'],
        ]]);
        session()->forget('kegiatan_draft');

        return redirect('/booking/kegiatan/selesai');
    }

    // =====================================
    // STEP 3: HALAMAN SELESAI KEGIATAN (pengajuan dikirim, menunggu approval)
    // =====================================

    public function selesaiKegiatan()
    {
        $done = session('kegiatan_done');

        if (!$done) {
            return redirect('/dashboard');
        }

        $room = Room::find($done['room_id']);

        return view('bookings.selesai-kegiatan', compact('done', 'room'));
    }

    // =====================================
    // BATALKAN PENGAJUAN KEGIATAN (dari step konfirmasi)
    // =====================================

    public function batalKegiatan()
    {
        $draft = session('kegiatan_draft');

        if ($draft && !empty($draft['surat_temp'])) {
            \Storage::disk('public')->delete($draft['surat_temp']);
        }

        session()->forget('kegiatan_draft');

        return redirect('/booking/kegiatan')->with('success', 'Pengajuan dibatalkan.');
    }

    // =====================================
    // LIST BOOKING PENDING (admin)
    // =====================================

    public function pendingBookings()
    {
        $bookings = Booking::where('status', 'pending')->get();
        return view('admin.bookings.pending', compact('bookings'));
    }

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
            ->with('rooms', 'kegiatan')
            ->orderBy('tanggal', 'desc')
            ->orderBy('jam_mulai', 'desc')
            ->get();

        return view('bookings.history', compact('bookings'));
    }

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