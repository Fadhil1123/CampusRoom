<?php

use Illuminate\Support\Facades\Route;
use App\Models\Room;
use App\Models\Booking;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('welcome');
});

//test route relasi database
Route::get('/test-room', function () {

    $rooms = Room::with('schedules')->get();

    return $rooms;

});

Route::get('/test-booking', function () {

    $bookings = Booking::with('rooms')->get();

    return $bookings;

});

//route auth
Route::get('/login', [AuthController::class, 'loginForm']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout']);

Route::get('/dashboard', function () {

    if (!session()->has('user_id')) {
        return redirect('/login');
    }

    return 'LOGIN BERHASIL';

});

//test middleware admin
Route::get('/admin', function () {

    return 'HALAMAN ADMIN';

})->middleware('admin');

//route resource untuk room
Route::get('/rooms', [RoomController::class, 'index'])
    ->middleware('admin');

Route::get('/rooms/create', [RoomController::class, 'create'])
    ->middleware('admin');

Route::post('/rooms/store', [RoomController::class, 'store'])
    ->middleware('admin');

Route::get('/rooms/edit/{id}', [RoomController::class, 'edit'])
    ->middleware('admin');

Route::put('/rooms/update/{id}', [RoomController::class, 'update'])
    ->middleware('admin');

Route::get('/rooms/delete/{id}', [RoomController::class, 'destroy'])
    ->middleware('admin');

//route resource untuk schedule
Route::get('/schedules', [ScheduleController::class, 'index'])
    ->middleware('admin');

Route::get('/schedules/create', [ScheduleController::class, 'create'])
    ->middleware('admin');

Route::post('/schedules/store', [ScheduleController::class, 'store'])
    ->middleware('admin');

Route::get('/schedules/edit/{id}', [ScheduleController::class, 'edit'])
    ->middleware('admin');

Route::put('/schedules/update/{id}', [ScheduleController::class, 'update'])
    ->middleware('admin');

Route::get('/schedules/delete/{id}', [ScheduleController::class, 'destroy'])
    ->middleware('admin');

//route booking perkuliahan
Route::get('/booking/perkuliahan', [BookingController::class, 'createPerkuliahan']);

Route::post('/booking/perkuliahan/store', [BookingController::class, 'storePerkuliahan']);

//route booking kegiatan
Route::get('/booking/kegiatan', [BookingController::class, 'createKegiatan']);

Route::post('/booking/kegiatan/store', [BookingController::class, 'storeKegiatan']);

//admin route approve/reject booking
Route::get('/admin/bookings', [BookingController::class, 'pendingBookings']);

Route::get('/admin/bookings/{id}/approve', [BookingController::class, 'approveBooking']);

Route::get('/admin/bookings/{id}/reject', [BookingController::class, 'rejectBooking']);

//route dashboard admin
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware('admin');

//route dashboard user
Route::get('/my-bookings', [BookingController::class, 'myBookings']);

//route admin lihat semua booking
Route::get('/admin/all-bookings', [BookingController::class, 'allBookings'])
    ->middleware('admin');