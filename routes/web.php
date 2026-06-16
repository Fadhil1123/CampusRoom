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

    // Rooms
    Route::get('/rooms',      [RoomController::class, 'index']);
    Route::get('/rooms/{id}', [RoomController::class, 'show']);

    // Pilih tipe booking
    Route::get('/booking', fn() => view('bookings.pilih'));

    // ===== BOOKING PERKULIAHAN: STEP 1 - 2 - 3 =====
    Route::get('/booking/perkuliahan',             [BookingController::class, 'createPerkuliahan']);
    Route::post('/booking/perkuliahan/konfirmasi', [BookingController::class, 'konfirmasiPerkuliahan']);
    Route::get('/booking/perkuliahan/konfirmasi',  [BookingController::class, 'showKonfirmasiPerkuliahan']);
    Route::post('/booking/perkuliahan/store',      [BookingController::class, 'storePerkuliahan']);
    Route::get('/booking/perkuliahan/selesai',     [BookingController::class, 'selesaiPerkuliahan']);

    // ===== BOOKING KEGIATAN: STEP 1 - 2 - 3 (MULTI-ROOM) =====
    Route::get('/booking/kegiatan',             [BookingController::class, 'createKegiatan']);
    Route::post('/booking/kegiatan/konfirmasi', [BookingController::class, 'konfirmasiKegiatan']);
    Route::get('/booking/kegiatan/konfirmasi',  [BookingController::class, 'showKonfirmasiKegiatan']);
    Route::post('/booking/kegiatan/store',      [BookingController::class, 'storeKegiatan']);
    Route::get('/booking/kegiatan/selesai',     [BookingController::class, 'selesaiKegiatan']);
    Route::post('/booking/kegiatan/batal',      [BookingController::class, 'batalKegiatan']);

    // Cek ketersediaan realtime (AJAX)
    Route::post('/booking/cek-ketersediaan',       [BookingController::class, 'cekKetersediaan']);       // single room
    Route::post('/booking/cek-ketersediaan-multi', [BookingController::class, 'cekKetersediaanMulti']);   // multi room

    // Download template surat
    Route::get('/booking/download-template', [BookingController::class, 'downloadTemplate']);

    // Riwayat & detail
    Route::get('/booking/history',              [BookingController::class, 'myBookings']);
    Route::get('/booking/detail/{id}',          [BookingController::class, 'detailBooking']);
    Route::post('/booking/{id}/batal',          [BookingController::class, 'batalkanBookingUser']);

    // ✅ FIX: Jadwal Saya — route yang sebelumnya belum ada
    Route::get('/jadwal-saya',                  [DashboardController::class, 'jadwalSaya']);

});

// ADMIN
Route::middleware('admin')->group(function () {

    Route::get('/admin', fn() => 'HALAMAN ADMIN');
    Route::get('/admin/dashboard', [DashboardController::class, 'adminDashboard']);

    Route::get('/rooms/create',       [RoomController::class, 'create']);
    Route::post('/rooms/store',       [RoomController::class, 'store']);
    Route::get('/rooms/edit/{id}',    [RoomController::class, 'edit']);
    Route::put('/rooms/update/{id}',  [RoomController::class, 'update']);
    Route::get('/rooms/delete/{id}',  [RoomController::class, 'destroy']);

    Route::get('/schedules',             [ScheduleController::class, 'index']);
    Route::get('/schedules/create',      [ScheduleController::class, 'create']);
    Route::post('/schedules/store',      [ScheduleController::class, 'store']);
    Route::get('/schedules/edit/{id}',   [ScheduleController::class, 'edit']);
    Route::put('/schedules/update/{id}', [ScheduleController::class, 'update']);
    Route::get('/schedules/delete/{id}', [ScheduleController::class, 'destroy']);

    Route::get('/admin/bookings',              [BookingController::class, 'pendingBookings']);
    Route::get('/admin/bookings/{id}/approve', [BookingController::class, 'approveBooking']);
    Route::get('/admin/bookings/{id}/reject',  [BookingController::class, 'rejectBooking']);
    Route::get('/admin/all-bookings',          [BookingController::class, 'allBookings']);

    Route::get('/admin/kegiatan',              [KegiatanController::class, 'index']);
    Route::get('/admin/kegiatan/edit/{id}',    [KegiatanController::class, 'edit']);
    Route::put('/admin/kegiatan/update/{id}',  [KegiatanController::class, 'update']);
    Route::get('/admin/kegiatan/delete/{id}',  [KegiatanController::class, 'destroy']);

});