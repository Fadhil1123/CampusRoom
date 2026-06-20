<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Booking;
use App\Models\Room;
use App\Models\BookingRoom;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminBookingDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_delete_booking_via_delete_method()
    {
        // Create an admin user
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        // Create a regular user
        $user = User::factory()->create([
            'role' => 'user',
        ]);

        // Create a room
        $room = Room::create([
            'nama_ruangan' => 'R. 101',
            'kapasitas' => 40,
            'status' => 'tersedia',
        ]);

        // Create a booking
        $booking = Booking::create([
            'user_id' => $user->user_id,
            'jenis' => 'perkuliahan',
            'tanggal' => '2026-06-25',
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '10:00:00',
            'status' => 'approved',
        ]);

        // Link booking to room
        BookingRoom::create([
            'booking_id' => $booking->booking_id,
            'room_id' => $room->room_id,
        ]);

        $this->assertDatabaseHas('bookings', ['booking_id' => $booking->booking_id]);
        $this->assertDatabaseHas('booking_rooms', ['booking_id' => $booking->booking_id]);

        // Access delete route as admin using DELETE request
        $response = $this->withSession(['user' => $admin])
            ->delete("/admin/booking/{$booking->booking_id}/hapus");

        $response->assertRedirect('/admin/all-bookings');
        $response->assertSessionHas('success', 'Booking berhasil dihapus.');

        // Verify it was deleted
        $this->assertDatabaseMissing('bookings', ['booking_id' => $booking->booking_id]);
        $this->assertDatabaseMissing('booking_rooms', ['booking_id' => $booking->booking_id]);
    }
}
