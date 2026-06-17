<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\Room;

class ScheduleController extends Controller
{
    // =====================================
    // INDEX - list semua jadwal
    // =====================================

    public function index(Request $request)
    {
        $query = Schedule::with('room');

        // Filter pencarian
        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function($qb) use ($q) {
                $qb->where('mata_kuliah', 'like', "%$q%")
                   ->orWhere('dosen', 'like', "%$q%");
            });
        }

        // Filter ruangan
        if ($request->filled('room_id')) {
            $query->where('room_id', $request->room_id);
        }

        // Filter hari
        if ($request->filled('hari')) {
            $query->where('hari', $request->hari);
        }

        $schedules = $query->orderBy('hari')->orderBy('jam_mulai')->paginate(10);
        $rooms     = Room::all();

        return view('schedules.index', compact('schedules', 'rooms'));
    }

    // =====================================
    // STORE - simpan jadwal baru
    // =====================================

    public function store(Request $request)
    {
        // ✅ FIX: validate dulu sebelum create
        $request->validate([
            'room_id'    => 'required|exists:rooms,room_id',
            'mata_kuliah'=> 'required|string|max:100',
            'dosen'      => 'required|string|max:100',
            'hari'       => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat',
            'jam_mulai'  => 'required',
            'jam_selesai'=> 'required|after:jam_mulai',
        ]);

        // Cek bentrok jadwal di ruangan + hari yang sama
        $bentrok = Schedule::where('room_id', $request->room_id)
            ->where('hari', $request->hari)
            ->where(function ($q) use ($request) {
                $q->where('jam_mulai', '<', $request->jam_selesai)
                  ->where('jam_selesai', '>', $request->jam_mulai);
            })
            ->exists();

        if ($bentrok) {
            return back()
                ->withInput()
                ->with('error', 'Jadwal bentrok! Ruangan sudah dipakai pada hari dan jam tersebut.');
        }

        Schedule::create([
            'room_id'    => $request->room_id,
            'mata_kuliah'=> $request->mata_kuliah,
            'dosen'      => $request->dosen,
            'hari'       => $request->hari,
            'jam_mulai'  => $request->jam_mulai,
            'jam_selesai'=> $request->jam_selesai,
        ]);

        return redirect('/schedules')->with('success', 'Jadwal berhasil ditambahkan!');
    }

    // =====================================
    // UPDATE - ubah jadwal
    // =====================================

    public function update(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);

        $request->validate([
            'room_id'    => 'required|exists:rooms,room_id',
            'mata_kuliah'=> 'required|string|max:100',
            'dosen'      => 'required|string|max:100',
            'hari'       => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat',
            'jam_mulai'  => 'required',
            'jam_selesai'=> 'required|after:jam_mulai',
        ]);

        // Cek bentrok — kecualikan diri sendiri
        $bentrok = Schedule::where('room_id', $request->room_id)
            ->where('hari', $request->hari)
            ->where('schedule_id', '!=', $id)
            ->where(function ($q) use ($request) {
                $q->where('jam_mulai', '<', $request->jam_selesai)
                  ->where('jam_selesai', '>', $request->jam_mulai);
            })
            ->exists();

        if ($bentrok) {
            return back()
                ->withInput()
                ->with('error', 'Jadwal bentrok! Ruangan sudah dipakai pada hari dan jam tersebut.');
        }

        $schedule->update([
            'room_id'    => $request->room_id,
            'mata_kuliah'=> $request->mata_kuliah,
            'dosen'      => $request->dosen,
            'hari'       => $request->hari,
            'jam_mulai'  => $request->jam_mulai,
            'jam_selesai'=> $request->jam_selesai,
        ]);

        return redirect('/schedules')->with('success', 'Jadwal berhasil diperbarui!');
    }

    // =====================================
    // DESTROY - hapus jadwal
    // =====================================

    public function destroy($id)
    {
        Schedule::findOrFail($id)->delete();
        return redirect('/schedules')->with('success', 'Jadwal berhasil dihapus!');
    }

    // =====================================
    // CEK BENTROK (AJAX - dari modal tambah)
    // =====================================

    public function cekBentrok(Request $request)
    {
        $bentrok = Schedule::where('room_id', $request->room_id)
            ->where('hari', $request->hari)
            ->when($request->filled('exclude_id'), fn($q) => $q->where('schedule_id', '!=', $request->exclude_id))
            ->where(function ($q) use ($request) {
                $q->where('jam_mulai', '<', $request->jam_selesai)
                  ->where('jam_selesai', '>', $request->jam_mulai);
            })
            ->first();

        if ($bentrok) {
            return response()->json([
                'status'  => 'conflict',
                'message' => 'Bentrok dengan ' . $bentrok->mata_kuliah . ' (' . substr($bentrok->jam_mulai,0,5) . '-' . substr($bentrok->jam_selesai,0,5) . ')',
            ]);
        }

        return response()->json(['status' => 'ok', 'message' => 'Tidak ada bentrok.']);
    }

    // =====================================
    // CREATE & EDIT (form terpisah — fallback jika modal gagal)
    // =====================================

    public function create()
    {
        $rooms = Room::all();
        return view('schedules.create', compact('rooms'));
    }

    public function edit($id)
    {
        $schedule = Schedule::findOrFail($id);
        $rooms    = Room::all();
        return view('schedules.edit', compact('schedule', 'rooms'));
    }
}