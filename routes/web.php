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

// PUBLIC
Route::get('/', fn() => view('landing'));

// TEST
Route::get('/test-room',    fn() => Room::with('schedules')->get());
Route::get('/test-booking', fn() => Booking::with('rooms')->get());

// AUTH
Route::get('/login',  [AuthController::class, 'loginForm']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout']);

// USER (mahasiswa)
Route::middleware('auth.custom')->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'userDashboard']);

    // Rooms — index & detail bisa diakses mahasiswa
    Route::get('/rooms',      [RoomController::class, 'index']);
    Route::get('/rooms/{id}', [RoomController::class, 'show']);

    // Halaman pilih tipe booking (perkuliahan / kegiatan)
    Route::get('/booking', function () {
        return view('bookings.pilih');
    });

    // Booking perkuliahan
    Route::get('/booking/perkuliahan',        [BookingController::class, 'createPerkuliahan']);
    Route::post('/booking/perkuliahan/store', [BookingController::class, 'storePerkuliahan']);

    // Cek ketersediaan realtime (AJAX)
    Route::post('/booking/cek-ketersediaan',  [BookingController::class, 'cekKetersediaan']);

    // Booking kegiatan
    Route::get('/booking/kegiatan',           [BookingController::class, 'createKegiatan']);
    Route::post('/booking/kegiatan/store',    [BookingController::class, 'storeKegiatan']);

    // Riwayat
    Route::get('/booking/history',            [BookingController::class, 'myBookings']);

});

// ADMIN
Route::middleware('admin')->group(function () {

    Route::get('/admin', fn() => 'HALAMAN ADMIN');
    Route::get('/admin/dashboard', [DashboardController::class, 'adminDashboard']);

    // CRUD room — hanya admin
    Route::get('/rooms/create',        [RoomController::class, 'create']);
    Route::post('/rooms/store',        [RoomController::class, 'store']);
    Route::get('/rooms/edit/{id}',     [RoomController::class, 'edit']);
    Route::put('/rooms/update/{id}',   [RoomController::class, 'update']);
    Route::get('/rooms/delete/{id}',   [RoomController::class, 'destroy']);

    Route::get('/schedules',              [ScheduleController::class, 'index']);
    Route::get('/schedules/create',       [ScheduleController::class, 'create']);
    Route::post('/schedules/store',       [ScheduleController::class, 'store']);
    Route::get('/schedules/edit/{id}',    [ScheduleController::class, 'edit']);
    Route::put('/schedules/update/{id}',  [ScheduleController::class, 'update']);
    Route::get('/schedules/delete/{id}',  [ScheduleController::class, 'destroy']);

    Route::get('/admin/bookings',                      [BookingController::class, 'pendingBookings']);
    Route::get('/admin/bookings/{id}/approve',         [BookingController::class, 'approveBooking']);
    Route::get('/admin/bookings/{id}/reject',          [BookingController::class, 'rejectBooking']);
    Route::get('/admin/all-bookings',                  [BookingController::class, 'allBookings']);

    Route::get('/admin/kegiatan',                      [KegiatanController::class, 'index']);
    Route::get('/admin/kegiatan/edit/{id}',            [KegiatanController::class, 'edit']);
    Route::put('/admin/kegiatan/update/{id}',          [KegiatanController::class, 'update']);
    Route::get('/admin/kegiatan/delete/{id}',          [KegiatanController::class, 'destroy']);

});