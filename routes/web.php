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
    return view('welcome');
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
| USER ROUTE
|--------------------------------------------------------------------------
*/

Route::middleware('auth.custom')->group(function () {

    // dashboard user
    Route::get('/my-bookings', [BookingController::class, 'myBookings']);

    // booking perkuliahan
    Route::get(
        '/booking/perkuliahan',
        [BookingController::class, 'createPerkuliahan']
    );

    Route::post(
        '/booking/perkuliahan/store',
        [BookingController::class, 'storePerkuliahan']
    );

    // booking kegiatan
    Route::get(
        '/booking/kegiatan',
        [BookingController::class, 'createKegiatan']
    );

    Route::post(
        '/booking/kegiatan/store',
        [BookingController::class, 'storeKegiatan']
    );

});

/*
|--------------------------------------------------------------------------
| ADMIN ROUTE
|--------------------------------------------------------------------------
*/

Route::middleware('admin')->group(function () {

    // test admin
    Route::get('/admin', function () {

        return 'HALAMAN ADMIN';

    });

    // dashboard admin
    Route::get(
        '/dashboard',
        [DashboardController::class, 'index']
    );

    // room
    Route::get('/rooms', [RoomController::class, 'index']);

    Route::get('/rooms/create', [RoomController::class, 'create']);

    Route::post('/rooms/store', [RoomController::class, 'store']);

    Route::get('/rooms/edit/{id}', [RoomController::class, 'edit']);

    Route::put('/rooms/update/{id}', [RoomController::class, 'update']);

    Route::get('/rooms/delete/{id}', [RoomController::class, 'destroy']);

    // schedule
    Route::get('/schedules', [ScheduleController::class, 'index']);

    Route::get('/schedules/create', [ScheduleController::class, 'create']);

    Route::post('/schedules/store', [ScheduleController::class, 'store']);

    Route::get('/schedules/edit/{id}', [ScheduleController::class, 'edit']);

    Route::put('/schedules/update/{id}', [ScheduleController::class, 'update']);

    Route::get('/schedules/delete/{id}', [ScheduleController::class, 'destroy']);

    // approval booking
    Route::get(
        '/admin/bookings',
        [BookingController::class, 'pendingBookings']
    );

    Route::get(
        '/admin/bookings/{id}/approve',
        [BookingController::class, 'approveBooking']
    );

    Route::get(
        '/admin/bookings/{id}/reject',
        [BookingController::class, 'rejectBooking']
    );

    // semua booking
    Route::get(
        '/admin/all-bookings',
        [BookingController::class, 'allBookings']
    );

    // kegiatan
    Route::get(
        '/admin/kegiatan',
        [KegiatanController::class, 'index']
    )->middleware('admin');

    Route::get(
        '/admin/kegiatan/edit/{id}',
        [KegiatanController::class, 'edit']
    )->middleware('admin');

    Route::put(
        '/admin/kegiatan/update/{id}',
        [KegiatanController::class, 'update']
    )->middleware('admin');

    Route::get(
        '/admin/kegiatan/delete/{id}',
        [KegiatanController::class, 'destroy']
    )->middleware('admin');

    // download template surat
    Route::get(
        '/download-template-surat',
        [BookingController::class, 'downloadTemplate']
    );

});