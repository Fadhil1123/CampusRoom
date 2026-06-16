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
            ->where('status', 'approved')
            ->count();

        $bookingMenunggu = Booking::where('user_id', $userId)
            ->where('status', 'pending')
            ->count();

        $bookingDitolak = Booking::where('user_id', $userId)
            ->where('status', 'rejected')
            ->count();

        $jadwalMendatang = Booking::where('user_id', $userId)
            ->whereIn('status', ['approved', 'pending'])
            ->whereDate('tanggal', '>=', now()->toDateString())
            ->with('rooms', 'kegiatan')
            ->orderBy('tanggal', 'asc')
            ->orderBy('jam_mulai', 'asc')
            ->take(5)
            ->get();

        $rekomendasiRuangan = Room::where('status', 'tersedia')
            ->take(4)
            ->get();

        return view('dashboard.index', compact(
            'bookingAktif',
            'bookingDisetujui',
            'bookingMenunggu',
            'bookingDitolak',
            'jadwalMendatang',
            'rekomendasiRuangan'
        ));
    }

    // =====================================
    // JADWAL SAYA (halaman jadwal mendatang)
    // =====================================

    public function jadwalSaya()
    {
        $userId = session('user')->user_id;

        $jadwalSaya = Booking::where('user_id', $userId)
            ->whereIn('status', ['approved', 'pending'])
            ->whereDate('tanggal', '>=', now()->toDateString())
            ->with('rooms', 'kegiatan')
            ->orderBy('tanggal', 'asc')
            ->orderBy('jam_mulai', 'asc')
            ->get();

        $jadwalTerdekat  = $jadwalSaya->first();
        $jumlahJadwal    = $jadwalSaya->count();
        $jumlahDisetujui = $jadwalSaya->where('status', 'approved')->count();
        $jumlahMenunggu  = $jadwalSaya->where('status', 'pending')->count();

        return view('bookings.jadwal', compact(
            'jadwalSaya',
            'jadwalTerdekat',
            'jumlahJadwal',
            'jumlahDisetujui',
            'jumlahMenunggu'
        ));
    }

    // =====================================
    // DASHBOARD ADMIN
    // =====================================

    public function adminDashboard()
    {
        $totalUser            = User::count();
        $totalBooking         = Booking::count();
        $pendingBooking       = Booking::where('status', 'pending')->count();
        $approvedBooking      = Booking::where('status', 'approved')->count();
        $rejectedBooking      = Booking::where('status', 'rejected')->count();
        $totalRoom            = Room::count();
        $totalKegiatan        = Kegiatan::count();
        $totalPerkuliahan     = Booking::where('jenis', 'perkuliahan')->count();
        $totalBookingKegiatan = Booking::where('jenis', 'kegiatan')->count();

        return view('dashboard.admin', compact(
            'totalUser',
            'totalBooking',
            'pendingBooking',
            'approvedBooking',
            'rejectedBooking',
            'totalRoom',
            'totalKegiatan',
            'totalPerkuliahan',
            'totalBookingKegiatan'
        ));
    }
}