<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Booking;
use App\Models\User;
use App\Models\Kegiatan;

class DashboardController extends Controller
{
    // =====================================
    // DASHBOARD MAHASISWA
    // =====================================

    public function userDashboard()
    {
        $userId = session('user')->user_id;

        $bookingAktif = Booking::where('user_id', $userId)
            ->whereIn('status', ['approved', 'pending'])
            ->whereDate('tanggal', '>=', now()->toDateString())
            ->count();

        $bookingDisetujui = Booking::where('user_id', $userId)
            ->where('status', 'approved')->count();

        $bookingMenunggu = Booking::where('user_id', $userId)
            ->where('status', 'pending')->count();

        $bookingDitolak = Booking::where('user_id', $userId)
            ->where('status', 'rejected')->count();

        $jadwalMendatang = Booking::where('user_id', $userId)
            ->whereIn('status', ['approved', 'pending'])
            ->whereDate('tanggal', '>=', now()->toDateString())
            ->with('rooms', 'kegiatan')
            ->orderBy('tanggal', 'asc')
            ->orderBy('jam_mulai', 'asc')
            ->take(5)->get();

        $rekomendasiRuangan = Room::where('status', 'tersedia')->take(4)->get();

        return view('dashboard.index', compact(
            'bookingAktif', 'bookingDisetujui', 'bookingMenunggu', 'bookingDitolak',
            'jadwalMendatang', 'rekomendasiRuangan'
        ));
    }

    // =====================================
    // JADWAL SAYA
    // =====================================

    public function jadwalSaya()
    {
        $userId = session('user')->user_id;

        $jadwalSaya = Booking::where('user_id', $userId)
            ->whereIn('status', ['approved', 'pending'])
            ->whereDate('tanggal', '>=', now()->toDateString())
            ->with(['rooms', 'kegiatan'])
            ->orderBy('tanggal', 'asc')
            ->orderBy('jam_mulai', 'asc')
            ->get();

        $jadwalTerdekat  = $jadwalSaya->first();
        $jumlahJadwal    = $jadwalSaya->count();
        $jumlahDisetujui = $jadwalSaya->where('status', 'approved')->count();
        $jumlahMenunggu  = $jadwalSaya->where('status', 'pending')->count();

        return view('bookings.jadwal', compact(
            'jadwalSaya', 'jadwalTerdekat', 'jumlahJadwal', 'jumlahDisetujui', 'jumlahMenunggu'
        ));
    }

    // =====================================
    // DASHBOARD ADMIN
    // =====================================

    public function adminDashboard()
    {
        // ── Stat cards ──────────────────────────────────────────
        $totalRoom       = Room::count();
        $bookingHariIni  = Booking::whereDate('tanggal', now()->toDateString())->count();
        $pendingBooking  = Booking::where('status', 'pending')->count();
        $ruanganAktif    = Room::where('status', 'tersedia')->count();

        // ── Global booking counts ────────────────────────────────
        $totalBooking         = Booking::count();
        $approvedBooking      = Booking::where('status', 'approved')->count();
        $rejectedBooking      = Booking::where('status', 'rejected')->count();
        $totalUser            = User::count();
        $totalKegiatan        = Kegiatan::count();
        $totalPerkuliahan     = Booking::where('jenis', 'perkuliahan')->count();
        $totalBookingKegiatan = Booking::where('jenis', 'kegiatan')->count();

        // ── Pending bookings untuk tabel approval ────────────────
        $pendingBookings = Booking::where('status', 'pending')
            ->with('user', 'rooms', 'kegiatan')
            ->orderBy('booking_id', 'desc')
            ->take(5)
            ->get();

        // ── Aktivitas terbaru (semua status, 5 terakhir) ─────────
        $aktivitasTerbaru = Booking::with('user', 'rooms')
            ->orderBy('booking_id', 'desc')
            ->take(8)
            ->get();

        // ── Chart: booking per hari dalam 7 hari terakhir ────────
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $tanggal = now()->subDays($i);
            $chartData[] = [
                'label' => $tanggal->locale('id')->translatedFormat('D'),
                'count' => Booking::whereDate('tanggal', $tanggal->toDateString())->count(),
            ];
        }

        // ── Status ruangan overview (semua ruangan) ──────────────
        $rooms = Room::orderBy('nama_ruangan')->get();

        // Tandai ruangan yang sedang dibooking hari ini
        $roomsBookedToday = Booking::join('booking_rooms', 'bookings.booking_id', '=', 'booking_rooms.booking_id')
            ->where('bookings.status', 'approved')
            ->whereDate('bookings.tanggal', now()->toDateString())
            ->pluck('booking_rooms.room_id')
            ->toArray();

        return view('dashboard.admin', compact(
            'totalRoom',
            'bookingHariIni',
            'pendingBooking',
            'ruanganAktif',
            'totalBooking',
            'approvedBooking',
            'rejectedBooking',
            'totalUser',
            'totalKegiatan',
            'totalPerkuliahan',
            'totalBookingKegiatan',
            'pendingBookings',
            'aktivitasTerbaru',
            'chartData',
            'rooms',
            'roomsBookedToday'
        ));
    }
}