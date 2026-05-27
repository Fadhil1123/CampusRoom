<?php

use Illuminate\Support\Facades\Route;
use App\Models\Room;
use App\Models\Booking;
use App\Http\Controllers\AuthController;

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