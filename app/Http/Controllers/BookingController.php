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

        // Bisa preselect 1 ruangan dari query string (akan jadi salah satu checkbox tercentang)
        $selectedRoomId = $request->filled('room_id') ? (int) $request->room_id : null;

        // Tanggal paling awal yang bisa dipilih (H-2)
        $minTanggal = now()->addDays(2)->toDateString();

        return view('bookings.kegiatan', compact('rooms', 'selectedRoomId', 'minTanggal'));
    }

    // =====================================
    // STEP 1 -> STEP 2 KEGIATAN: VALIDASI MULTI-ROOM, UPLOAD SEMENTARA, SIMPAN SESSION
    // =====================================

    public function konfirmasiKegiatan(Request $request)
    {
        $request->validate([
            'room_ids'          => 'required|array|min:1',
            'room_ids.*'        => 'required|exists:rooms,room_id',
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

        $roomIds = array_unique($request->room_ids);

        // ===== Validasi H-2 =====
        $minimalTanggal  = now()->startOfDay()->addDays(2);
        $tanggalKegiatan = Carbon::parse($request->tanggal)->startOfDay();

        if ($tanggalKegiatan->lt($minimalTanggal)) {
            return back()
                ->with('error', 'Tanggal terlalu dekat! Pengajuan minimal H-2. Tanggal paling awal yang bisa dipilih: ' . $minimalTanggal->locale('id')->translatedFormat('l, d F Y'))
                ->withInput();
        }

        // =====================================================
        // LOGIC MULTI-ROOM BOOKING
        // IF jenis = kegiatan DAN >1 ruangan dipilih
        // DAN seluruh ruangan tersedia (tidak bentrok jadwal/booking approved)
        // THEN lanjut. ELSE seluruh booking ditolak + tampilkan peringatan.
        // =====================================================
        $roomsBentrok = [];

        foreach ($roomIds as $roomId) {
            $bentrokBooking = Booking::join('booking_rooms', 'bookings.booking_id', '=', 'booking_rooms.booking_id')
                ->where('booking_rooms.room_id', $roomId)
                ->where('bookings.status', 'approved')
                ->whereDate('bookings.tanggal', $request->tanggal)
                ->where(function ($q) use ($request) {
                    $q->where('bookings.jam_mulai', '<', $request->jam_selesai)
                      ->where('bookings.jam_selesai', '>', $request->jam_mulai);
                })
                ->exists();

            if ($bentrokBooking) {
                $room = Room::find($roomId);
                $roomsBentrok[] = $room->nama_ruangan ?? "Ruangan #$roomId";
            }
        }

        // ELSE: jika salah satu ruangan bentrok -> seluruh booking ditolak
        if (!empty($roomsBentrok)) {
            return back()
                ->with('error', 'Booking ditolak! Ruangan berikut sudah dibooking pada waktu tersebut: ' . implode(', ', $roomsBentrok) . '. Silakan pilih ruangan atau waktu lain.')
                ->withInput();
        }

        // ===== Upload surat sementara ke folder temp =====
        $pathSurat = $request->file('surat')->store('surat/temp', 'public');

        session(['kegiatan_draft' => [
            'room_ids'          => $roomIds,
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

        $rooms = Room::whereIn('room_id', $draft['room_ids'])->get();

        return view('bookings.konfirmasi-kegiatan', compact('draft', 'rooms'));
    }

    // =====================================
    // STEP 2 -> STEP 3: SIMPAN KEGIATAN + BOOKING (MULTI-ROOM) KE DB
    // =====================================

    public function storeKegiatan(Request $request)
    {
        $draft = session('kegiatan_draft');

        if (!$draft) {
            return redirect('/booking/kegiatan')->with('error', 'Sesi booking tidak ditemukan, silakan isi ulang form.');
        }

        $roomIds = $draft['room_ids'];

        // ===== Re-cek bentrok terakhir untuk SEMUA ruangan (race condition) =====
        $roomsBentrok = [];

        foreach ($roomIds as $roomId) {
            $bentrokBooking = Booking::join('booking_rooms', 'bookings.booking_id', '=', 'booking_rooms.booking_id')
                ->where('booking_rooms.room_id', $roomId)
                ->where('bookings.status', 'approved')
                ->whereDate('bookings.tanggal', $draft['tanggal'])
                ->where(function ($q) use ($draft) {
                    $q->where('bookings.jam_mulai', '<', $draft['jam_selesai'])
                      ->where('bookings.jam_selesai', '>', $draft['jam_mulai']);
                })
                ->exists();

            if ($bentrokBooking) {
                $room = Room::find($roomId);
                $roomsBentrok[] = $room->nama_ruangan ?? "Ruangan #$roomId";
            }
        }

        // ELSE: jika salah satu ruangan bentrok -> seluruh booking ditolak, hapus draft
        if (!empty($roomsBentrok)) {
            if (!empty($draft['surat_temp'])) {
                \Storage::disk('public')->delete($draft['surat_temp']);
            }
            session()->forget('kegiatan_draft');

            return redirect('/booking/kegiatan')
                ->with('error', 'Booking ditolak! Ruangan berikut baru saja dibooking pihak lain: ' . implode(', ', $roomsBentrok) . '. Silakan pilih ruangan atau waktu lain.');
        }

        // Pindahkan file surat dari temp ke folder permanen
        $pathTemp  = $draft['surat_temp'];
        $pathFinal = str_replace('surat/temp/', 'surat/', $pathTemp);

        if (\Storage::disk('public')->exists($pathTemp)) {
            \Storage::disk('public')->move($pathTemp, $pathFinal);
        }

        // THEN: sistem membuat SATU data booking
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
            'status'      => 'pending', // status booking = pending (menunggu approval admin)
            'surat'       => $pathFinal,
            'approved_by' => null,
            'approved_at' => null,
        ]);

        // Sistem menyimpan relasi booking dan SETIAP ruangan pada tabel booking_rooms
        foreach ($roomIds as $roomId) {
            BookingRoom::create([
                'booking_id' => $booking->booking_id,
                'room_id'    => $roomId,
            ]);
        }

        session(['kegiatan_done' => [
            'booking_id'    => $booking->booking_id,
            'nama_kegiatan' => $draft['nama_kegiatan'],
            'room_ids'      => $roomIds,
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

        $rooms = Room::whereIn('room_id', $done['room_ids'])->get();

        return view('bookings.selesai-kegiatan', compact('done', 'rooms'));
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
    // CEK KETERSEDIAAN MULTI-ROOM (AJAX) - dipakai form kegiatan
    // Cek beberapa room_id sekaligus, return status per ruangan
    // =====================================

    public function cekKetersediaanMulti(Request $request)
    {
        $roomIds    = $request->room_ids ?? [];
        $tanggal    = $request->tanggal;
        $jamMulai   = $request->jam_mulai;
        $jamSelesai = $request->jam_selesai;

        if (empty($roomIds) || !$tanggal || !$jamMulai || !$jamSelesai) {
            return response()->json(['status' => 'incomplete']);
        }

        $results = [];
        $allAvailable = true;

        foreach ($roomIds as $roomId) {
            $room = Room::find($roomId);
            if (!$room) continue;

            $bentrokBooking = Booking::join('booking_rooms', 'bookings.booking_id', '=', 'booking_rooms.booking_id')
                ->where('booking_rooms.room_id', $roomId)
                ->where('bookings.status', 'approved')
                ->whereDate('bookings.tanggal', $tanggal)
                ->where(function ($q) use ($jamMulai, $jamSelesai) {
                    $q->where('bookings.jam_mulai', '<', $jamSelesai)
                      ->where('bookings.jam_selesai', '>', $jamMulai);
                })
                ->first();

            if ($bentrokBooking) {
                $allAvailable = false;
                $results[] = [
                    'room_id' => $roomId,
                    'nama'    => $room->nama_ruangan,
                    'status'  => 'conflict',
                    'detail'  => substr($bentrokBooking->jam_mulai, 0, 5) . ' - ' . substr($bentrokBooking->jam_selesai, 0, 5),
                ];
            } else {
                $results[] = [
                    'room_id' => $roomId,
                    'nama'    => $room->nama_ruangan,
                    'status'  => 'available',
                ];
            }
        }

        return response()->json([
            'status' => $allAvailable ? 'available' : 'conflict',
            'rooms'  => $results,
        ]);
    }

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

    // =====================================
    // DETAIL BOOKING USER
    // =====================================

    public function detailBooking($id)
    {
        $userId  = session('user')->user_id;
        $booking = Booking::where('booking_id', $id)
            ->where('user_id', $userId)
            ->with('rooms', 'kegiatan')
            ->firstOrFail();

        return view('bookings.detail', compact('booking'));
    }

    // =====================================
    // BATALKAN BOOKING (dari halaman riwayat, hanya pending)
    // =====================================

    public function batalkanBookingUser($id)
    {
        $userId  = session('user')->user_id;
        $booking = Booking::where('booking_id', $id)
            ->where('user_id', $userId)
            ->where('status', 'pending')
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking tidak ditemukan atau tidak bisa dibatalkan.',
            ], 404);
        }

        // Hapus file surat dari storage jika ada
        if ($booking->surat) {
            \Storage::disk('public')->delete($booking->surat);
        }

        // Hapus relasi booking_rooms
        \App\Models\BookingRoom::where('booking_id', $booking->booking_id)->delete();

        // Hapus data kegiatan jika ada
        if ($booking->kegiatan_id) {
            \App\Models\Kegiatan::find($booking->kegiatan_id)?->delete();
        }

        $booking->delete();

        return response()->json(['success' => true, 'message' => 'Booking berhasil dibatalkan.']);
    }
}