<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Booking;
use App\Models\User;
use App\Models\Kegiatan;

class DashboardController extends Controller
{
    public function index()
    {

        $totalUser = User::count();

        $totalBooking = Booking::count();

        $pendingBooking = Booking::where(
            'status',
            'pending'
        )->count();

        $approvedBooking = Booking::where(
            'status',
            'approved'
        )->count();

        $rejectedBooking = Booking::where(
            'status',
            'rejected'
        )->count();

        $totalRoom = Room::count();

        $totalKegiatan = Kegiatan::count();

        $totalPerkuliahan = Booking::where(
            'jenis',
            'perkuliahan'
        )->count();

        $totalBookingKegiatan = Booking::where(
            'jenis',
            'kegiatan'
        )->count();

        return view(
            'dashboard.index',
            compact(
                'totalUser',
                'totalBooking',
                'pendingBooking',
                'approvedBooking',
                'rejectedBooking',
                'totalRoom',
                'totalKegiatan',
                'totalPerkuliahan',
                'totalBookingKegiatan'
            )
        );
    }
}