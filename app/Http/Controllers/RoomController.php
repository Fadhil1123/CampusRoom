<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Booking;
use Carbon\Carbon;

class RoomController extends Controller
{
    // Daftar semua room (mahasiswa & admin)
    public function index(Request $request)
    {
        $query = Room::query();

        if ($request->filled('kapasitas')) {
            $kap = $request->kapasitas;
            if ($kap === 'small')  $query->where('kapasitas', '<=', 20);
            if ($kap === 'medium') $query->whereBetween('kapasitas', [21, 50]);
            if ($kap === 'large')  $query->where('kapasitas', '>', 50);
        }

        $rooms = $query->orderBy('room_id')->get();
        return view('rooms.index', compact('rooms'));
    }

    // Detail ruangan + jadwal mingguan
    public function show($id)
    {
        $room = Room::findOrFail($id);

        // Jadwal tetap milik ruangan ini
        $schedules = $room->schedules;

        // Booking minggu ini untuk ruangan ini
        $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $endOfWeek   = Carbon::now()->endOfWeek(Carbon::SUNDAY);

        $bookings = Booking::whereHas('rooms', function ($q) use ($id) {
                $q->where('rooms.room_id', $id);
            })
            ->whereBetween('tanggal', [$startOfWeek, $endOfWeek])
            ->with('kegiatan')
            ->get();

        $todayShort = Carbon::now()->format('D'); // Mon, Tue, ...

        return view('rooms.show', compact('room', 'schedules', 'bookings', 'todayShort'));
    }

    // Form tambah room (admin)
    public function create()
    {
        return view('rooms.create');
    }

    // Simpan room baru (admin)
    public function store(Request $request)
    {
        $request->validate([
            'nama_ruangan' => 'required|string|max:100',
            'kapasitas'    => 'required|integer|min:1',
            'status'       => 'required|in:tersedia,tidak tersedia',
        ]);

        Room::create([
            'nama_ruangan' => $request->nama_ruangan,
            'kapasitas'    => $request->kapasitas,
            'status'       => $request->status,
        ]);

        return redirect('/rooms')->with('success', 'Ruangan berhasil ditambahkan.');
    }

    // Form edit room (admin)
    public function edit($id)
    {
        $room = Room::findOrFail($id);
        return view('rooms.edit', compact('room'));
    }

    // Update room (admin)
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_ruangan' => 'required|string|max:100',
            'kapasitas'    => 'required|integer|min:1',
            'status'       => 'required|in:tersedia,tidak tersedia',
        ]);

        $room = Room::findOrFail($id);
        $room->update([
            'nama_ruangan' => $request->nama_ruangan,
            'kapasitas'    => $request->kapasitas,
            'status'       => $request->status,
        ]);

        return redirect('/rooms')->with('success', 'Ruangan berhasil diupdate.');
    }

    // Hapus room (admin)
    public function destroy($id)
    {
        $room = Room::findOrFail($id);
        $room->delete();
        return redirect('/rooms')->with('success', 'Ruangan berhasil dihapus.');
    }
}