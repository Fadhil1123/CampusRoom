<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;

class RoomController extends Controller
{
    // Menampilkan semua room (untuk mahasiswa & admin)
    public function index(Request $request)
    {
        $query = Room::query();

        // Filter kapasitas
        if ($request->filled('kapasitas')) {
            $kap = $request->kapasitas;
            if ($kap === 'small')  $query->where('kapasitas', '<=', 20);
            if ($kap === 'medium') $query->whereBetween('kapasitas', [21, 50]);
            if ($kap === 'large')  $query->where('kapasitas', '>', 50);
        }

        $rooms = $query->orderBy('room_id')->get();

        return view('rooms.index', compact('rooms'));
    }

    // Menampilkan form tambah room
    public function create()
    {
        return view('rooms.create');
    }

    // Menyimpan room baru
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

    // Menampilkan form edit room
    public function edit($id)
    {
        $room = Room::findOrFail($id);
        return view('rooms.edit', compact('room'));
    }

    // Update room
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

    // Hapus room
    public function destroy($id)
    {
        $room = Room::findOrFail($id);
        $room->delete();
        return redirect('/rooms')->with('success', 'Ruangan berhasil dihapus.');
    }
}