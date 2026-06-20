<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookingMassAssignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_model_allows_mass_assignment_of_approved_by_and_approved_at()
    {
        $user = User::factory()->create();
        $admin = User::factory()->create(['role' => 'admin']);

        $now = now();
        $booking = Booking::create([
            'user_id' => $user->user_id,
            'jenis' => 'perkuliahan',
            'tanggal' => '2026-06-25',
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '10:00:00',
            'status' => 'approved',
            'approved_by' => $admin->user_id,
            'approved_at' => $now,
        ]);

        $this->assertEquals($admin->user_id, $booking->approved_by);
        $this->assertEquals($now->toDateTimeString(), $booking->approved_at->toDateTimeString());
    }
}
