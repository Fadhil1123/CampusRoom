<?php

use Illuminate\Support\Facades\Route;
use App\Models\Room;
use App\Models\Booking;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KegiatanController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTE
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('landing');
});

/*
|--------------------------------------------------------------------------
| TEST ROUTE
|--------------------------------------------------------------------------
*/

Route::get('/test-room', function () {
    $rooms = Room::with('schedules')->get();
    return $rooms;
});

Route::get('/test-booking', function () {
    $bookings = Booking::with('rooms')->get();
    return $bookings;
});

/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
*/

Route::get('/login', [AuthController::class, 'loginForm']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout']);

/*
|--------------------------------------------------------------------------
| USER ROUTE (mahasiswa)
|--------------------------------------------------------------------------
*/

Route::middleware('auth.custom')->group(function () {

    // Dashboard mahasiswa
    Route::get('/dashboard', [DashboardController::class, 'userDashboard']);

    // Daftar ruangan — mahasiswa bisa lihat & booking
    Route::get('/rooms', [RoomController::class, 'index']);

    // Booking perkuliahan
    Route::get('/booking/perkuliahan', [BookingController::class, 'createPerkuliahan']);
    Route::post('/booking/perkuliahan/store', [BookingController::class, 'storePerkuliahan']);

    // Booking kegiatan
    Route::get('/booking/kegiatan', [BookingController::class, 'createKegiatan']);
    Route::post('/booking/kegiatan/store', [BookingController::class, 'storeKegiatan']);

    // History booking
    Route::get('/booking/history', [BookingController::class, 'myBookings']);

});

/*
|--------------------------------------------------------------------------
| ADMIN ROUTE
|--------------------------------------------------------------------------
*/

Route::middleware('admin')->group(function () {

    Route::get('/admin', function () {
        return 'HALAMAN ADMIN';
    });

    // Dashboard admin
    Route::get('/admin/dashboard', [DashboardController::class, 'adminDashboard']);

    // CRUD room (admin only)
    Route::get('/rooms/create', [RoomController::class, 'create']);
    Route::post('/rooms/store', [RoomController::class, 'store']);
    Route::get('/rooms/edit/{id}', [RoomController::class, 'edit']);
    Route::put('/rooms/update/{id}', [RoomController::class, 'update']);
    Route::get('/rooms/delete/{id}', [RoomController::class, 'destroy']);

    // Schedule
    Route::get('/schedules', [ScheduleController::class, 'index']);

});