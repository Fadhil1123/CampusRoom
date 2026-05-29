<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Booking;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalRoom = Room::count();

        $totalBooking = Booking::count();

        $pendingBooking = Booking::where(
            'status',
            'pending'
        )->count();

        $approvedBooking = Booking::where(
            'status',
            'approved'
        )->count();

        $totalUser = User::count();

        return view('dashboard.index', compact(

            'totalRoom',
            'totalBooking',
            'pendingBooking',
            'approvedBooking',
            'totalUser'

        ));
    }
}