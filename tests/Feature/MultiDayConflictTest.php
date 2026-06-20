<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Booking;
use App\Models\Room;
use App\Models\BookingRoom;
use App\Models\Kegiatan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MultiDayConflictTest extends TestCase
{
    use RefreshDatabase;

    public function test_multi_day_booking_conflicts_properly()
    {
        $user = User::factory()->create();

        $room = Room::create([
            'nama_ruangan' => 'Aula Utama',
            'kapasitas' => 200,
            'status' => 'tersedia',
        ]);

        // Create an existing approved booking that runs from 2026-06-25 to 2026-06-27
        $existingKegiatan = Kegiatan::create([
            'nama_kegiatan' => 'Event Besar',
            'penyelenggara' => 'Himpunan',
            'tanggal_selesai' => '2026-06-27',
            'perkiraan_peserta' => 150,
        ]);

        $existingBooking = Booking::create([
            'user_id' => $user->user_id,
            'kegiatan_id' => $existingKegiatan->kegiatan_id,
            'tanggal' => '2026-06-25',
            'jam_mulai' => '08:00:00',
            'jam_selesai' => '12:00:00',
            'jenis' => 'kegiatan',
            'status' => 'approved',
        ]);

        BookingRoom::create([
            'booking_id' => $existingBooking->booking_id,
            'room_id' => $room->room_id,
        ]);

        // Verify conflict on day 2 (2026-06-26) using the check availability API
        $response = $this->withSession(['user' => $user])
            ->postJson('/booking/cek-ketersediaan-multi', [
                'room_ids' => [$room->room_id],
                'tanggal' => '2026-06-26',
                'tanggal_selesai' => '2026-06-26',
                'jam_mulai' => '09:00:00',
                'jam_selesai' => '11:00:00',
            ]);

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertEquals('conflict', $data['rooms'][0]['status']);
    }
}
