<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;

class RoomController extends Controller
{
    // Menampilkan semua room
    public function index()
    {
        $rooms = Room::all();

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
        Room::create([
            'nama_ruangan' => $request->nama_ruangan,
            'kapasitas' => $request->kapasitas,
            'status' => $request->status,
        ]);

        return redirect('/rooms');
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
        $room = Room::findOrFail($id);

        $room->update([
            'nama_ruangan' => $request->nama_ruangan,
            'kapasitas' => $request->kapasitas,
            'status' => $request->status,
        ]);

        return redirect('/rooms');
    }

    // Hapus room
    public function destroy($id)
    {
        $room = Room::findOrFail($id);

        $room->delete();

        return redirect('/rooms');
    }
}