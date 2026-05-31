<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\Room;

class ScheduleController extends Controller
{
    // Menampilkan semua schedule
    public function index()
    {
        $schedules = Schedule::with('room')->get();

        return view('schedules.index', compact('schedules'));
    }

    // Form tambah schedule
    public function create()
    {
        $rooms = Room::all();

        return view('schedules.create', compact('rooms'));
    }

    // Simpan schedule
    public function store(Request $request)
    {
        Schedule::create([
            'room_id' => $request->room_id,
            'mata_kuliah' => $request->mata_kuliah,
            'dosen' => $request->dosen,
            'hari' => $request->hari,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
        ]);

        $request->validate([

        'room_id' => 'required',

        'hari' => 'required',

        'jam_mulai' => 'required',

        'jam_selesai' => 'required|after:jam_mulai',

    ]);

        return redirect('/schedules');
    }

    // Form edit
    public function edit($id)
    {
        $schedule = Schedule::findOrFail($id);

        $rooms = Room::all();

        return view('schedules.edit', compact('schedule', 'rooms'));
    }

    // Update schedule
    public function update(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);

        $schedule->update([
            'room_id' => $request->room_id,
            'mata_kuliah' => $request->mata_kuliah,
            'dosen' => $request->dosen,
            'hari' => $request->hari,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
        ]);

        $request->validate([

        'room_id' => 'required',

        'hari' => 'required',

        'jam_mulai' => 'required',

        'jam_selesai' => 'required|after:jam_mulai',

    ]);

        return redirect('/schedules');
    }

    // Hapus schedule
    public function destroy($id)
    {
        $schedule = Schedule::findOrFail($id);

        $schedule->delete();

        return redirect('/schedules');
    }
}