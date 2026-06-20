<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Booking;
use App\Models\Room;
use App\Models\BookingRoom;
use App\Models\Schedule;
use App\Models\Kegiatan;

class BookingController extends Controller
{
    // =====================================
    // HELPER: konversi nama hari Inggris -> Indonesia
    // =====================================

    private function convertHari(string $tanggal): ?string
    {
        $map = [
            'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu',
        ];

        return $map[date('l', strtotime($tanggal))] ?? null;
    }

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
    // CEK KETERSEDIAAN (AJAX realtime) - single room
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

        $hariIndonesia = $this->convertHari($tanggal);

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
                'status'  => 'conflict',
                'message' => 'Bentrok dengan jadwal kuliah',
                'detail'  => $bentrokSchedule->mata_kuliah . ' (' . substr($bentrokSchedule->jam_mulai, 0, 5) . ' - ' . substr($bentrokSchedule->jam_selesai, 0, 5) . ')',
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
    // CEK KETERSEDIAAN (AJAX realtime) - multi room, dipakai form Kegiatan
    // =====================================

    public function cekKetersediaanMulti(Request $request)
    {
        $roomIds    = $request->room_ids;
        $tanggal    = $request->tanggal;
        $jamMulai   = $request->jam_mulai;
        $jamSelesai = $request->jam_selesai;

        if (!$roomIds || !is_array($roomIds) || count($roomIds) === 0 || !$tanggal || !$jamMulai || !$jamSelesai) {
            return response()->json(['status' => 'incomplete']);
        }

        $hariIndonesia = $this->convertHari($tanggal);
        $hasil         = [];

        foreach ($roomIds as $roomId) {
            $room = Room::find($roomId);
            if (!$room) {
                continue;
            }

            // Ruangan sedang non-aktif (maintenance dll)
            if ($room->status !== 'tersedia') {
                $hasil[] = [
                    'room_id' => $room->room_id,
                    'nama'    => $room->nama_ruangan,
                    'status'  => 'conflict',
                    'detail'  => 'Ruangan sedang tidak tersedia',
                ];
                continue;
            }

            // Bentrok jadwal kuliah tetap
            $bentrokSchedule = Schedule::where('room_id', $roomId)
                ->where('hari', $hariIndonesia)
                ->where(function ($q) use ($jamMulai, $jamSelesai) {
                    $q->where('jam_mulai', '<', $jamSelesai)
                      ->where('jam_selesai', '>', $jamMulai);
                })
                ->first();

            if ($bentrokSchedule) {
                $hasil[] = [
                    'room_id' => $room->room_id,
                    'nama'    => $room->nama_ruangan,
                    'status'  => 'conflict',
                    'detail'  => $bentrokSchedule->mata_kuliah . ' (' . substr($bentrokSchedule->jam_mulai, 0, 5) . '-' . substr($bentrokSchedule->jam_selesai, 0, 5) . ')',
                ];
                continue;
            }

            // Bentrok booking lain (approved / pending)
            $bentrokBooking = Booking::join('booking_rooms', 'bookings.booking_id', '=', 'booking_rooms.booking_id')
                ->where('booking_rooms.room_id', $roomId)
                ->whereIn('bookings.status', ['approved', 'pending'])
                ->whereDate('bookings.tanggal', $tanggal)
                ->where(function ($q) use ($jamMulai, $jamSelesai) {
                    $q->where('bookings.jam_mulai', '<', $jamSelesai)
                      ->where('bookings.jam_selesai', '>', $jamMulai);
                })
                ->first();

            if ($bentrokBooking) {
                $hasil[] = [
                    'room_id' => $room->room_id,
                    'nama'    => $room->nama_ruangan,
                    'status'  => 'conflict',
                    'detail'  => substr($bentrokBooking->jam_mulai, 0, 5) . ' - ' . substr($bentrokBooking->jam_selesai, 0, 5),
                ];
                continue;
            }

            $hasil[] = [
                'room_id' => $room->room_id,
                'nama'    => $room->nama_ruangan,
                'status'  => 'available',
                'detail'  => null,
            ];
        }

        return response()->json(['status' => 'ok', 'rooms' => $hasil]);
    }

    // =====================================
    // STEP 2: KONFIRMASI BOOKING PERKULIAHAN
    // (nama function disamakan dengan route 'konfirmasiPerkuliahan')
    // =====================================

    public function konfirmasiPerkuliahan(Request $request)
    {
        $request->validate([
            'room_id'     => 'required',
            'mata_kuliah' => 'required|string',
            'dosen'       => 'required|string',
            'tanggal'     => 'required|date',
            'jam_mulai'   => 'required',
            'jam_selesai' => 'required',
        ]);

        $room = Room::findOrFail($request->room_id);

        // Format hari & tanggal dalam Bahasa Indonesia
        $hariMap = [
            'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu',
        ];
        $bulanMap = [
            'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret',
            'April' => 'April', 'May' => 'Mei', 'June' => 'Juni', 'July' => 'Juli',
            'August' => 'Agustus', 'September' => 'September', 'October' => 'Oktober',
            'November' => 'November', 'December' => 'Desember',
        ];

        $timestamp = strtotime($request->tanggal);
        $hariEn    = date('l', $timestamp);
        $bulanEn   = date('F', $timestamp);

        $tanggalFormatted = ($hariMap[$hariEn] ?? $hariEn) . ', '
            . date('d', $timestamp) . ' '
            . ($bulanMap[$bulanEn] ?? $bulanEn) . ' '
            . date('Y', $timestamp);

        // Hitung durasi (jam)
        $mulai     = strtotime($request->jam_mulai);
        $selesai   = strtotime($request->jam_selesai);
        $durasiJam = max(0, round(($selesai - $mulai) / 3600, 1));

        return view('bookings.perkuliahan_konfirmasi', [
            'room'              => $room,
            'mata_kuliah'       => $request->mata_kuliah,
            'dosen'             => $request->dosen,
            'tanggal'           => $request->tanggal,
            'tanggal_formatted' => $tanggalFormatted,
            'jam_mulai'         => $request->jam_mulai,
            'jam_selesai'       => $request->jam_selesai,
            'durasi'            => $durasiJam,
        ]);
    }

    // Halaman konfirmasi diakses lewat GET (mis. refresh halaman).
    // Karena data perkuliahan dikirim lewat hidden input (bukan session),
    // tidak ada data yang bisa ditampilkan lagi -> balik ke step 1.
    public function showKonfirmasiPerkuliahan()
    {
        return redirect('/booking/perkuliahan');
    }

    // =====================================
    // STEP 3 (PROSES): SIMPAN BOOKING PERKULIAHAN
    // =====================================

    public function storePerkuliahan(Request $request)
    {
        $request->validate([
            'room_id'     => 'required',
            'tanggal'     => 'required|date',
            'jam_mulai'   => 'required',
            'jam_selesai' => 'required',
        ]);

        $hariIndonesia = $this->convertHari($request->tanggal);

        $bentrokSchedule = Schedule::where('room_id', $request->room_id)
            ->where('hari', $hariIndonesia)
            ->where(function ($q) use ($request) {
                $q->where('jam_mulai', '<', $request->jam_selesai)
                  ->where('jam_selesai', '>', $request->jam_mulai);
            })
            ->exists();

        if ($bentrokSchedule) {
            return redirect('/booking/perkuliahan')->with('error', 'Ruangan sedang dipakai jadwal kuliah!');
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
            return redirect('/booking/perkuliahan')->with('error', 'Ruangan sudah dibooking pada waktu tersebut!');
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

        // Simpan id booking supaya bisa diambil lagi di halaman "selesai"
        session(['last_booking_id_perkuliahan' => $booking->booking_id]);

        return redirect('/booking/perkuliahan/selesai');
    }

    // =====================================
    // STEP 3 (TAMPILAN): BOOKING PERKULIAHAN SELESAI
    // =====================================

    public function selesaiPerkuliahan()
    {
        $id = session('last_booking_id_perkuliahan');

        if (!$id) {
            return redirect('/booking/perkuliahan');
        }

        $booking = Booking::with('rooms')->find($id);

        if (!$booking) {
            return redirect('/booking/perkuliahan');
        }

        return view('bookings.perkuliahan_berhasil', compact('booking'));
    }

    // =====================================
    // FORM BOOKING KEGIATAN
    // =====================================

    public function createKegiatan(Request $request)
    {
        $rooms          = Room::all();
        $selectedRoomId = $request->filled('room_id') ? (int) $request->room_id : null;
        $minTanggal     = now()->addDays(2)->format('Y-m-d');

        return view('bookings.kegiatan', compact('rooms', 'selectedRoomId', 'minTanggal'));
    }

    // =====================================
    // STEP 2: KONFIRMASI BOOKING KEGIATAN (multi-room + upload surat)
    // =====================================

    public function konfirmasiKegiatan(Request $request)
    {
        $request->validate([
            'room_ids'          => 'required|array|min:1',
            'nama_kegiatan'     => 'required|string',
            'penyelenggara'     => 'required|string',
            'tanggal'           => 'required|date',
            'tanggal_selesai'   => 'required|date|after_or_equal:tanggal',
            'jam_mulai'         => 'required',
            'jam_selesai'       => 'required',
            'perkiraan_peserta' => 'required|integer|min:1',
            'surat'             => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        // Aturan H-2
        $minimalTanggal = strtotime('+2 days', strtotime(date('Y-m-d')));
        if (strtotime($request->tanggal) < $minimalTanggal) {
            return back()->with('error', 'Booking kegiatan minimal H-2!')->withInput();
        }

        $roomIds = $request->room_ids;
        $rooms   = Room::whereIn('room_id', $roomIds)->get();

        if ($rooms->count() !== count($roomIds)) {
            return back()->with('error', 'Salah satu ruangan yang dipilih tidak ditemukan.')->withInput();
        }

        // File surat langsung disimpan, path-nya ditaruh di draft session
        // supaya tidak hilang saat user pindah ke halaman konfirmasi.
        $pathSurat = $request->file('surat')->store('surat', 'public');

        $draft = [
            'room_ids'          => array_map('intval', $roomIds),
            'nama_kegiatan'     => $request->nama_kegiatan,
            'deskripsi'         => $request->deskripsi,
            'penyelenggara'     => $request->penyelenggara,
            'tanggal'           => $request->tanggal,
            'tanggal_selesai'   => $request->tanggal_selesai,
            'jam_mulai'         => $request->jam_mulai,
            'jam_selesai'       => $request->jam_selesai,
            'perkiraan_peserta' => $request->perkiraan_peserta,
            'surat_path'        => $pathSurat,
            'surat_nama_asli'   => $request->file('surat')->getClientOriginalName(),
        ];

        session(['kegiatan_draft' => $draft]);

        return view('bookings.konfirmasi-kegiatan', [
            'draft' => $draft,
            'rooms' => $rooms,
        ]);
    }

    // Diakses lewat GET, misalnya saat halaman konfirmasi di-refresh.
    public function showKonfirmasiKegiatan()
    {
        $draft = session('kegiatan_draft');

        if (!$draft) {
            return redirect('/booking/kegiatan');
        }

        $rooms = Room::whereIn('room_id', $draft['room_ids'])->get();

        return view('bookings.konfirmasi-kegiatan', compact('draft', 'rooms'));
    }

    // =====================================
    // SIMPAN BOOKING KEGIATAN (final, ambil data dari draft session)
    // =====================================

    public function storeKegiatan(Request $request)
    {
        $draft = session('kegiatan_draft');

        if (!$draft) {
            return redirect('/booking/kegiatan')->with('error', 'Data booking tidak ditemukan, silakan isi ulang form.');
        }

        $roomIds = $draft['room_ids'];

        // =====================================
        // LOGIC MULTI-ROOM BOOKING
        // Cek SEMUA ruangan dulu sebelum menyimpan apapun.
        // Jika salah satu ruangan tidak tersedia / bentrok,
        // SELURUH booking ditolak (tidak ada yang disimpan).
        // =====================================
        foreach ($roomIds as $roomId) {
            $room = Room::find($roomId);

            if (!$room) {
                return back()->with('error', 'Salah satu ruangan yang dipilih tidak ditemukan.');
            }

            if ($room->status !== 'tersedia') {
                return back()->with('error', "Ruangan {$room->nama_ruangan} sedang tidak tersedia. Booking dibatalkan.");
            }

            $bentrokBooking = Booking::join('booking_rooms', 'bookings.booking_id', '=', 'booking_rooms.booking_id')
                ->where('booking_rooms.room_id', $roomId)
                ->whereIn('bookings.status', ['approved', 'pending'])
                ->whereDate('bookings.tanggal', $draft['tanggal'])
                ->where(function ($q) use ($draft) {
                    $q->where('bookings.jam_mulai', '<', $draft['jam_selesai'])
                      ->where('bookings.jam_selesai', '>', $draft['jam_mulai']);
                })
                ->exists();

            if ($bentrokBooking) {
                return back()->with('error', "Ruangan {$room->nama_ruangan} sudah dibooking pada tanggal & jam tersebut. Booking dibatalkan.");
            }

            $hariIndonesia = $this->convertHari($draft['tanggal']);

            $bentrokSchedule = Schedule::where('room_id', $roomId)
                ->where('hari', $hariIndonesia)
                ->where(function ($q) use ($draft) {
                    $q->where('jam_mulai', '<', $draft['jam_selesai'])
                      ->where('jam_selesai', '>', $draft['jam_mulai']);
                })
                ->exists();

            if ($bentrokSchedule) {
                return back()->with('error', "Ruangan {$room->nama_ruangan} bentrok dengan jadwal kuliah tetap. Booking dibatalkan.");
            }
        }

        // =====================================
        // SEMUA RUANGAN TERSEDIA & TIDAK BENTROK
        // -> Simpan satu data kegiatan + booking + relasi booking_rooms
        // =====================================
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
            'status'      => 'pending', // menunggu approval admin
            'surat'       => $draft['surat_path'],
            'approved_by' => null,
            'approved_at' => null,
        ]);

        foreach ($roomIds as $roomId) {
            BookingRoom::create([
                'booking_id' => $booking->booking_id,
                'room_id'    => $roomId,
            ]);
        }

        // Bersihkan draft, simpan id booking untuk halaman "selesai"
        session()->forget('kegiatan_draft');
        session(['last_kegiatan_booking_id' => $booking->booking_id]);

        return redirect('/booking/kegiatan/selesai');
    }

    // =====================================
    // STEP 3 (TAMPILAN): BOOKING KEGIATAN SELESAI / DIAJUKAN
    // =====================================

    public function selesaiKegiatan()
    {
        $id = session('last_kegiatan_booking_id');

        if (!$id) {
            return redirect('/dashboard');
        }

        $booking = Booking::with(['rooms', 'kegiatan'])->find($id);

        if (!$booking) {
            return redirect('/dashboard');
        }

        $done = [
            'nama_kegiatan' => $booking->kegiatan->nama_kegiatan ?? null,
        ];
        $rooms = $booking->rooms;

        return view('bookings.selesai-kegiatan', compact('done', 'rooms'));
    }

    // =====================================
    // BATALKAN PENGAJUAN KEGIATAN (sebelum disimpan, dari halaman konfirmasi)
    // =====================================

    public function batalKegiatan()
    {
        $draft = session('kegiatan_draft');

        if ($draft && !empty($draft['surat_path'])) {
            Storage::disk('public')->delete($draft['surat_path']);
        }

        session()->forget('kegiatan_draft');

        return redirect('/booking/kegiatan')->with('success', 'Pengajuan booking kegiatan dibatalkan.');
    }

    // =====================================
    // LIST BOOKING PENDING (admin)
    // =====================================

    public function pendingBookings(Request $request)
{
    $query = Booking::with(['rooms', 'user', 'kegiatan'])
        ->orderBy('booking_id', 'asc');

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    } else {
        $query->where('status', 'pending');
    }

    if ($request->filled('search')) {
        $s = $request->search;
        $query->where(function($q) use ($s) {
            $q->whereHas('user', fn($u) => $u->where('nama', 'like', "%$s%"))
              ->orWhere('booking_id', 'like', "%$s%");
        });
    }

    if ($request->filled('jenis'))
        $query->where('jenis', $request->jenis);

    if ($request->filled('dari'))
        $query->whereDate('tanggal', '>=', $request->dari);

    if ($request->filled('hingga'))
        $query->whereDate('tanggal', '<=', $request->hingga);

    if ($request->filled('room_id')) {
        $query->whereHas('rooms', fn($q) =>
            $q->where('rooms.room_id', $request->room_id));
    }

    $bookings = $query->get();
    $totalPending = $bookings->count();
    $rooms = Room::orderBy('nama_ruangan')->get();

    return view(
        'admin.bookings.pending',
        compact('bookings', 'totalPending', 'rooms')
    );
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
    // DETAIL BOOKING (user)
    // =====================================

    public function detailBooking($id)
    {
        $booking = Booking::with(['rooms', 'kegiatan'])->findOrFail($id);

        $user = session('user');

        // Hanya pemilik booking atau admin yang boleh lihat detailnya
        if ($user->role !== 'admin' && $booking->user_id != $user->user_id) {
            abort(403);
        }

        return view('bookings.detail', compact('booking'));
    }

    // detail booking (admin)
    public function adminDetailBooking($id)
    {
    $booking = Booking::with(['rooms', 'kegiatan', 'user'])->findOrFail($id);

    return view('admin.bookings.detail', compact('booking'));
    }

    // =====================================
    // BATALKAN BOOKING (user, dari halaman detail/riwayat - AJAX)
    // =====================================

    public function batalkanBookingUser($id)
    {
        $booking = Booking::find($id);
        $user    = session('user');

        if (!$booking || $booking->user_id != $user->user_id) {
            return response()->json(['success' => false, 'message' => 'Booking tidak ditemukan.']);
        }

        if ($booking->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Hanya booking berstatus pending yang bisa dibatalkan.']);
        }

        // Tabel bookings hanya punya status pending/approved/rejected,
        // jadi pembatalan oleh user disimpan sebagai 'rejected'.
        $booking->status = 'rejected';
        $booking->save();

        return response()->json(['success' => true]);
    }

    // =====================================
    // ALL BOOKINGS (admin)
    // =====================================

    public function allBookings(Request $request)
    {
        $query = Booking::with(['rooms', 'user', 'kegiatan'])
            ->orderBy('booking_id', 'desc');

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter jenis
        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        // Filter ruangan
        if ($request->filled('room_id')) {
            $query->whereHas('rooms', fn($q) => $q->where('rooms.room_id', $request->room_id));
        }

        // Filter tanggal dari-hingga
        if ($request->filled('dari')) {
            $query->whereDate('tanggal', '>=', $request->dari);
        }
        if ($request->filled('hingga')) {
            $query->whereDate('tanggal', '<=', $request->hingga);
        }

        // Search nama user atau kode booking
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn($u) => $u->where('nama', 'like', "%$search%"))
                  ->orWhere('booking_id', 'like', "%$search%");
            });
        }

        $totalBooking = $query->count();
        $bookings     = $query->paginate(10)->withQueryString();
        $rooms        = \App\Models\Room::orderBy('nama_ruangan')->get();

        return view('admin.bookings.all', compact('bookings', 'totalBooking', 'rooms'));
    }

    public function downloadTemplate()
    {
        $path = public_path('template/template_surat_peminjaman.docx');
        return response()->download($path);
    }

    //edit status admin

    public function editStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected'
        ]);

        $booking = Booking::findOrFail($id);

        $booking->status = $request->status;

        if ($request->status != 'pending') {
            $booking->approved_by = session('user')->user_id;
            $booking->approved_at = now();
        }

        $booking->save();

        return back()->with('success', 'Status booking berhasil diperbarui.');
    }

    //hapus booking admin
    public function hapusBooking($id)
    {
        $booking = Booking::findOrFail($id);
        $kegiatanId = $booking->kegiatan_id;

        // Hapus relasi room
        BookingRoom::where('booking_id', $booking->booking_id)->delete();

        // Hapus file surat jika ada
        if ($booking->surat) {
            Storage::disk('public')->delete($booking->surat);
        }

        $booking->delete();

        if ($kegiatanId) {
            Kegiatan::where('kegiatan_id', $kegiatanId)->delete();
        }

        return redirect('/admin/all-bookings')
            ->with('success', 'Booking berhasil dihapus.');
    }

    //bulk delete admin
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'booking_ids' => 'required|array|min:1',
            'booking_ids.*' => 'exists:bookings,booking_id'
        ]);

        foreach ($request->booking_ids as $id) {
            $booking = Booking::find($id);
            if ($booking) {
                $kegiatanId = $booking->kegiatan_id;

                BookingRoom::where('booking_id', $booking->booking_id)->delete();

                if ($booking->surat) {
                    Storage::disk('public')->delete($booking->surat);
                }

                $booking->delete();

                if ($kegiatanId) {
                    Kegiatan::where('kegiatan_id', $kegiatanId)->delete();
                }
            }
        }

        return redirect('/admin/all-bookings')
            ->with('success', count($request->booking_ids) . ' booking berhasil dihapus.');
    }
}